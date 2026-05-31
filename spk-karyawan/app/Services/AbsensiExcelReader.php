<?php

namespace App\Services;

/**
 * Membaca file .xlsx format absensi PT Cempaka Indah Abadi.
 * TIDAK menggunakan SimpleXMLElement::xpath() sama sekali.
 * Semua parsing menggunakan regex untuk menghindari error namespace prefix.
 */
class AbsensiExcelReader
{
    private const BULAN_MAP = [
        'JAN' => 1,  'JANUARI'   => 1,
        'FEB' => 2,  'FEBRUARI'  => 2,
        'MAR' => 3,  'MARET'     => 3,
        'APR' => 4,  'APRIL'     => 4,
        'MEI' => 5,  'MAY'       => 5,
        'JUN' => 6,  'JUNI'      => 6,
        'JUL' => 7,  'JULI'      => 7,
        'AGU' => 8,  'AGUSTUS'   => 8,  'AUG' => 8,
        'SEP' => 9,  'SEPTEMBER' => 9,
        'OKT' => 10, 'OKTOBER'   => 10, 'OCT' => 10,
        'NOV' => 11, 'NOVEMBER'  => 11,
        'DES' => 12, 'DESEMBER'  => 12, 'DEC' => 12,
    ];

    public function readByBulan(string $filePath, int $bulan): array
    {
        $loaded = $this->extractRows($filePath, $bulan);
        if ($loaded['error']) {
            return $this->err($loaded['error']);
        }

        return [
            'data'       => $this->parseAbsensiRows($loaded['rows']),
            'sheet_name' => $loaded['sheet_name'],
            'error'      => null,
            'warning'    => $loaded['warning'],
        ];
    }

    /**
     * Sama seperti readByBulan, tetapi mengembalikan detail kehadiran
     * PER-TANGGAL (bukan hanya total). Dipakai untuk fitur Rekap Absensi.
     *
     * Return:
     * [
     *   'days'       => [5, 6, 7, ...],            // nomor tanggal hari kerja
     *   'data'       => [ ['no'=>1,'nama'=>'PAIDI','hadir'=>[5,6,...],'total_hadir'=>26], ... ],
     *   'sheet_name' => 'JAN',
     *   'error'      => null,
     *   'warning'    => null,
     * ]
     */
    public function readDetailByBulan(string $filePath, int $bulan): array
    {
        $loaded = $this->extractRows($filePath, $bulan);
        if ($loaded['error']) {
            return [
                'days' => [], 'data' => [], 'sheet_name' => null,
                'error' => $loaded['error'], 'warning' => null,
            ];
        }

        $detail = $this->parseAbsensiRowsDetail($loaded['rows']);

        return [
            'days'       => $detail['days'],
            'data'       => $detail['data'],
            'sheet_name' => $loaded['sheet_name'],
            'error'      => null,
            'warning'    => $loaded['warning'],
        ];
    }

    /**
     * Buka file .xlsx, pilih sheet sesuai bulan, kembalikan baris mentahnya.
     * Logika pemilihan sheet dipakai bersama oleh readByBulan & readDetailByBulan.
     */
    private function extractRows(string $filePath, int $bulan): array
    {
        $fail = fn(string $msg) => ['rows' => [], 'sheet_name' => null, 'warning' => null, 'error' => $msg];

        if (!file_exists($filePath)) {
            return $fail('File tidak ditemukan.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return $fail('File tidak bisa dibuka. Pastikan format .xlsx valid.');
        }

        try {
            $sharedStrings = $this->readSharedStrings($zip);
            $sheetList     = $this->readSheetList($zip);

            // Cari sheet sesuai bulan
            $target  = null;
            $warning = null;
            foreach ($sheetList as $sheet) {
                if ((self::BULAN_MAP[strtoupper(trim($sheet['name']))] ?? null) === $bulan) {
                    $target = $sheet;
                    break;
                }
            }

            // Fallback ke sheet pertama
            if (!$target) {
                $target  = $sheetList[0] ?? null;
                $warning = $target
                    ? "Sheet bulan ke-{$bulan} tidak ditemukan. Menggunakan sheet '{$target['name']}'."
                    : null;
            }

            if (!$target) {
                $zip->close();
                return $fail('File tidak memiliki sheet yang bisa dibaca.');
            }

            $xml = $zip->getFromName($target['path']);
            $zip->close();

            if ($xml === false) {
                return $fail("Gagal membaca isi sheet '{$target['name']}'.");
            }

            return [
                'rows'       => $this->parseSheetXml($xml, $sharedStrings),
                'sheet_name' => $target['name'],
                'warning'    => $warning,
                'error'      => null,
            ];

        } catch (\Throwable $e) {
            $zip->close();
            return $fail('Gagal membaca file: ' . $e->getMessage());
        }
    }

    // ── Shared strings via regex (TANPA SimpleXMLElement) ────────

    private function readSharedStrings(\ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) return [];

        $strings = [];

        preg_match_all('/<si>(.*?)<\/si>/s', $xml, $siMatches);
        foreach ($siMatches[1] as $si) {
            preg_match_all('/<t(?:[^>]*)>(.*?)<\/t>/s', $si, $tMatches);
            $str = implode('', array_map(
                fn($t) => html_entity_decode($t, ENT_XML1 | ENT_QUOTES, 'UTF-8'),
                $tMatches[1]
            ));
            $strings[] = $str;
        }

        return $strings;
    }

    // ── Sheet list via regex (TANPA SimpleXMLElement) ────────────

    private function readSheetList(\ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/workbook.xml');
        if ($xml === false) {
            return [['name' => 'Sheet1', 'path' => 'xl/worksheets/sheet1.xml']];
        }

        $sheets = [];
        preg_match_all('/<sheet\s[^>]*>/i', $xml, $matches);

        foreach ($matches[0] as $i => $tag) {
            preg_match('/name=["\']([^"\']*)["\']/', $tag, $nameMatch);
            $name     = $nameMatch[1] ?? ('Sheet' . ($i + 1));
            $sheets[] = [
                'name' => $name,
                'path' => 'xl/worksheets/sheet' . ($i + 1) . '.xml',
            ];
        }

        return $sheets ?: [['name' => 'Sheet1', 'path' => 'xl/worksheets/sheet1.xml']];
    }

    // ── Parse sheet XML via regex (TANPA SimpleXMLElement) ───────

    private function parseSheetXml(string $xml, array $sharedStrings): array
    {
        $rows = [];

        preg_match_all('/<row\s[^>]*r="(\d+)"[^>]*>(.*?)<\/row>/s', $xml, $rowMatches, PREG_SET_ORDER);

        foreach ($rowMatches as $rowMatch) {
            $rowIdx  = (int) $rowMatch[1] - 1;
            $rowXml  = $rowMatch[2];
            $rowData = [];

            preg_match_all('/<c\s([^>]*)>(.*?)<\/c>/s', $rowXml, $cellMatches, PREG_SET_ORDER);

            foreach ($cellMatches as $cellMatch) {
                $attrs   = $cellMatch[1];
                $content = $cellMatch[2];

                // Ref kolom
                preg_match('/\br="([A-Z]+\d+)"/i', $attrs, $rMatch);
                $colIdx = $this->colToIndex($rMatch[1] ?? 'A1');

                // Tipe sel
                preg_match('/\bt="([^"]+)"/', $attrs, $tMatch);
                $type = $tMatch[1] ?? '';

                // Nilai <v>
                preg_match('/<v[^>]*>(.*?)<\/v>/s', $content, $vMatch);
                $raw = isset($vMatch[1]) ? trim($vMatch[1]) : null;

                if ($type === 'inlineStr') {
                    // Inline string: <is><t>...</t></is> (bisa beberapa <t> utk rich text)
                    preg_match_all('/<t[^>]*>(.*?)<\/t>/s', $content, $tParts);
                    $txt = isset($tParts[1]) ? implode('', $tParts[1]) : '';
                    $txt = html_entity_decode($txt, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    $value = ($txt === '') ? null : $txt;
                } elseif ($raw === null || $raw === '') {
                    $value = null;
                } elseif ($type === 's') {
                    $value = $sharedStrings[(int) $raw] ?? '';
                } elseif ($type === 'str') {
                    $value = null; // formula — abaikan
                } else {
                    $value = is_numeric($raw)
                        ? (str_contains($raw, '.') ? (float) $raw : (int) $raw)
                        : $raw;
                }

                $rowData[$colIdx] = $value;
            }

            if (!empty($rowData)) {
                $max = max(array_keys($rowData));
                for ($c = 0; $c <= $max; $c++) $rowData[$c] ??= null;
                ksort($rowData);
                $rows[$rowIdx] = array_values($rowData);
            }
        }

        ksort($rows);
        return array_values($rows);
    }

    // ── Parse rows → hasil akhir ──────────────────────────────────

    private function parseAbsensiRows(array $rows): array
    {
        if (empty($rows)) return [];

        $header = $rows[0];

        // Cari kolom NAMA
        $namaCol = 1;
        foreach ($header as $i => $h) {
            if (strtoupper(trim((string) ($h ?? ''))) === 'NAMA') {
                $namaCol = $i;
                break;
            }
        }

        // Kolom tanggal = header berisi integer 1–31
        $dateCols = [];
        foreach ($header as $i => $h) {
            if (is_int($h) && $h >= 1 && $h <= 31) $dateCols[] = $i;
        }

        $result = [];
        for ($r = 1; $r < count($rows); $r++) {
            $row = $rows[$r];
            $no  = $row[0] ?? null;

            if (!is_int($no)) continue;

            $nama = trim((string) ($row[$namaCol] ?? ''));
            if ($nama === '') continue;

            $totalHadir = 0;
            foreach ($dateCols as $dc) {
                if (($row[$dc] ?? null) === 1) $totalHadir++;
            }

            $result[] = ['no' => $no, 'nama' => $nama, 'total_hadir' => $totalHadir];
        }

        return $result;
    }

    /**
     * Parse rows → detail kehadiran per-tanggal.
     * Mengembalikan daftar nomor tanggal hari kerja + tanggal hadir tiap karyawan.
     */
    private function parseAbsensiRowsDetail(array $rows): array
    {
        if (empty($rows)) return ['days' => [], 'data' => []];

        $header = $rows[0];

        // Cari kolom NAMA
        $namaCol = 1;
        foreach ($header as $i => $h) {
            if (strtoupper(trim((string) ($h ?? ''))) === 'NAMA') {
                $namaCol = $i;
                break;
            }
        }

        // Kolom tanggal = header berisi integer 1–31 → map index kolom ke nomor tanggal
        $dateColMap = []; // [index_kolom => nomor_tanggal]
        foreach ($header as $i => $h) {
            if (is_int($h) && $h >= 1 && $h <= 31) $dateColMap[$i] = $h;
        }

        // Kolom ID (opsional) — untuk pencocokan berbasis id karyawan
        $idCol = null;
        foreach ($header as $i => $h) {
            if (strtoupper(trim((string) ($h ?? ''))) === 'ID') { $idCol = $i; break; }
        }

        $days = array_values($dateColMap);
        sort($days);

        $result = [];
        for ($r = 1; $r < count($rows); $r++) {
            $row = $rows[$r];

            $nama = trim((string) ($row[$namaCol] ?? ''));
            $idRaw = $idCol !== null ? ($row[$idCol] ?? null) : null;
            $id   = is_int($idRaw) ? $idRaw : (is_numeric($idRaw) ? (int) $idRaw : null);

            // Baris dianggap valid jika punya nama ATAU id
            if ($nama === '' && !$id) continue;

            $hadir     = [];   // semua hari hadir (termasuk yang terlambat)
            $terlambat = [];   // hari hadir tapi terlambat
            foreach ($dateColMap as $colIdx => $tanggal) {
                $v = $row[$colIdx] ?? null;
                $code = is_string($v) ? strtoupper(trim($v)) : $v;

                if ($code === 1 || $code === '1' || $code === 'H') {
                    $hadir[] = $tanggal;                      // hadir tepat waktu
                } elseif ($code === 'T') {
                    $hadir[] = $tanggal;                      // terlambat = tetap hadir
                    $terlambat[] = $tanggal;
                }
                // selain itu (kosong) = tidak hadir
            }
            sort($hadir);
            sort($terlambat);

            $result[] = [
                'id'              => $id,
                'nama'            => $nama,
                'hadir'           => $hadir,
                'terlambat'       => $terlambat,
                'total_hadir'     => count($hadir),
                'total_terlambat' => count($terlambat),
            ];
        }

        return ['days' => $days, 'data' => $result];
    }

    // ── Kolom Excel → index 0-based ──────────────────────────────

    private function colToIndex(string $cellRef): int
    {
        preg_match('/^([A-Z]+)/i', $cellRef, $m);
        $col = strtoupper($m[1] ?? 'A');
        $idx = 0;
        foreach (str_split($col) as $char) $idx = $idx * 26 + (ord($char) - 64);
        return $idx - 1;
    }

    private function err(string $msg): array
    {
        return ['data' => [], 'sheet_name' => null, 'error' => $msg, 'warning' => null];
    }
}