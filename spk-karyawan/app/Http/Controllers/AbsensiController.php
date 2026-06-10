<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Periode;
use App\Models\Karyawan;
use App\Services\AbsensiExcelReader;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    // ── Rekap Absensi: grid kehadiran harian per karyawan ────────

    public function rekap(Request $request)
    {
        // Daftar bulan-tahun yang sudah punya data absensi (untuk default & info)
        $bulanTersedia = Absensi::query()
            ->orderByDesc('tanggal')
            ->pluck('tanggal')
            ->map(fn($t) => [
                'tahun' => (int) Carbon::parse($t)->year,
                'bulan' => (int) Carbon::parse($t)->month,
            ])
            ->unique(fn($x) => $x['tahun'] . '-' . $x['bulan'])
            ->values();

        // Bulan & tahun terpilih: dari query, atau default ke data terbaru / bulan ini
        $bulan = (int) $request->input('bulan', 0);
        $tahun = (int) $request->input('tahun', 0);

        if ($bulan < 1 || $bulan > 12 || $tahun < 2000) {
            $first = $bulanTersedia->first();
            $bulan = $first['bulan'] ?? (int) now()->month;
            $tahun = $first['tahun'] ?? (int) now()->year;
        }

        // Karyawan aktif sebagai baris tabel
        $karyawanList = Karyawan::aktif()->orderBy('nama')->get();

        // Ambil semua absensi pada bulan & tahun terpilih
        $absensi = Absensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Hari kerja = tanggal-tanggal unik yang tercatat di bulan tsb
        $workingDays = $absensi
            ->map(fn($a) => (int) Carbon::parse($a->tanggal)->day)
            ->unique()
            ->sort()
            ->values()
            ->all();

        // Susun grid: [karyawan_id][nomor_tanggal] => ['status'=>..., 'terlambat'=>bool]
        $grid = [];
        foreach ($absensi as $a) {
            $hari = (int) Carbon::parse($a->tanggal)->day;
            $grid[$a->karyawan_id][$hari] = [
                'status'    => $a->status,
                'terlambat' => (bool) $a->terlambat,
            ];
        }

        $stat = [
            'total_karyawan' => $karyawanList->count(),
            'hari_kerja'     => count($workingDays),
        ];

        $labelBulan = $this->namaBulan($bulan) . ' ' . $tahun;

        return view('absensi.rekap', compact(
            'bulanTersedia', 'bulan', 'tahun', 'labelBulan',
            'karyawanList', 'workingDays', 'grid', 'stat'
        ));
    }

    // ── Menu Absensi: form import (pilih bulan & tahun sendiri) ──

    public function uploadFormIndex()
    {
        return view('absensi.upload-index');
    }

    /** Absensi pribadi — untuk karyawan & admin yang terhubung ke data karyawan */
    public function absensiPribadi(Request $request)
    {
        $karyawan = Auth::user()?->karyawan;

        $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli',
                      'Agustus','September','Oktober','November','Desember'];

        if (!$karyawan) {
            return view('absensi.pribadi', [
                'karyawan' => null, 'grid' => [], 'workingDays' => [],
                'stat' => [], 'labelBulan' => '—', 'bulanTersedia' => collect(),
                'bulan' => null, 'tahun' => null, 'namaBulan' => $namaBulan,
            ]);
        }

        // Bulan-tahun yang punya data absensi untuk karyawan ini
        $bulanTersedia = Absensi::where('karyawan_id', $karyawan->id)
            ->get()
            ->map(fn($a) => [
                'tahun' => (int) Carbon::parse($a->tanggal)->format('Y'),
                'bulan' => (int) Carbon::parse($a->tanggal)->format('n'),
            ])
            ->unique(fn($b) => $b['tahun'].'-'.$b['bulan'])
            ->sortByDesc(fn($b) => $b['tahun'] * 100 + $b['bulan'])
            ->values();

        $prevMonth    = now()->subMonth();
        $defaultBulan = (int) $prevMonth->format('n');
        $defaultTahun = (int) $prevMonth->format('Y');

        $bulan = (int) $request->input('bulan', $defaultBulan);
        $tahun = (int) $request->input('tahun', $defaultTahun);

        // Data absensi bulan ini
        $absensi = Absensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Hari kerja bulan ini (kecuali Minggu)
        $workingDays = [];
        $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            if (Carbon::create($tahun, $bulan, $d)->dayOfWeek !== 0) { // 0 = Sunday
                $workingDays[] = $d;
            }
        }

        // Grid [hari => ['status', 'terlambat']]
        $grid = [];
        foreach ($absensi as $a) {
            $day = (int) Carbon::parse($a->tanggal)->format('j');
            $grid[$day] = ['status' => $a->status, 'terlambat' => (bool) $a->terlambat];
        }

        $totalHadir     = $absensi->where('status', 'hadir')->count();
        $totalTerlambat = $absensi->where('terlambat', true)->count();

        $stat = [
            'total_hadir'      => $totalHadir,
            'total_terlambat'  => $totalTerlambat,
            'tidak_hadir'      => count($workingDays) - $totalHadir,
            'hari_kerja'       => count($workingDays),
        ];

        $labelBulan = ($namaBulan[$bulan] ?? $bulan).' '.$tahun;

        return view('absensi.pribadi', compact(
            'karyawan','grid','workingDays','stat','labelBulan','bulanTersedia','bulan','tahun','namaBulan'
        ));
    }

    public function uploadProsesIndex(Request $request)
    {
        $request->validate([
            'bulan'        => 'required|integer|min:1|max:12',
            'tahun'        => 'required|integer|min:2000|max:2100',
            'file_absensi' => 'required|file|mimes:xlsx|max:10240',
        ], [], [
            'bulan' => 'bulan',
            'tahun' => 'tahun',
        ]);

        return $this->importAbsensi(
            $request->file('file_absensi')->getRealPath(),
            (int) $request->bulan,
            (int) $request->tahun,
            redirect()->route('absensi.rekap', ['bulan' => $request->bulan, 'tahun' => $request->tahun])
        );
    }

    // ── Halaman Penilaian: form upload (periode sudah diketahui) ─

    public function uploadForm(Periode $periode)
    {
        return view('absensi.upload', compact('periode'));
    }

    public function uploadProses(Request $request, Periode $periode)
    {
        $request->validate([
            'file_absensi' => 'required|file|mimes:xlsx|max:10240',
        ]);

        return $this->importAbsensi(
            $request->file('file_absensi')->getRealPath(),
            $periode->bulan,
            $periode->tahun,
            redirect()->route('penilaian.index', $periode)
        );
    }

    // ── Inti proses ───────────────────────────────────────────────

    /**
     * Import absensi berdasarkan bulan & tahun (TIDAK butuh periode aktif).
     *
     * - Data kehadiran per-tanggal disimpan ke tabel absensi.
     * - Nilai Kehadiran & Kedisiplinan tampil sebagai pilihan default di form
     *   Input Nilai (tersimpan ke penilaian setelah direktur klik Simpan).
     */
    private function importAbsensi(string $filePath, int $bulan, int $tahun, RedirectResponse $redirectTarget)
    {
        $reader = new AbsensiExcelReader();
        $result = $reader->readDetailByBulan($filePath, $bulan);

        if ($result['error']) {
            return back()->with('error', $result['error']);
        }

        if (empty($result['data'])) {
            return back()->with('error', 'Tidak ada data karyawan yang ditemukan di file Excel.');
        }

        $workingDays = $result['days']; // nomor tanggal hari kerja, mis. [5,6,...,31]

        // Periode opsional — hanya dipakai untuk menyusun pesan info
        $periode = Periode::where('bulan', $bulan)->where('tahun', $tahun)->first();

        $berhasil       = 0;
        $tidakDitemukan = [];

        foreach ($result['data'] as $item) {
            $id             = $item['id'] ?? null;
            $nama           = $item['nama'];
            $hadirDays      = $item['hadir'];          // termasuk yang terlambat
            $terlambatDays  = $item['terlambat'] ?? [];
            $totalHadir     = (int) $item['total_hadir'];
            $totalTerlambat = (int) ($item['total_terlambat'] ?? 0);

            // 1. Cocokkan ke karyawan: utamakan ID, lalu fallback ke NAMA
            $karyawan = null;
            if ($id) {
                $karyawan = Karyawan::find($id);
            }
            if (!$karyawan && $nama !== '') {
                $karyawan = Karyawan::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower(trim($nama))])->first();
            }
            if (!$karyawan) {
                $tidakDitemukan[] = $id ? "ID {$id}" . ($nama !== '' ? " ({$nama})" : '') : $nama;
                continue;
            }

            // 2. Simpan kehadiran PER-TANGGAL (hadir / terlambat / alpha)
            $this->simpanAbsensiHarian($karyawan->id, $bulan, $tahun, $workingDays, $hadirDays, $terlambatDays);
            $berhasil++;
        }

        $label      = $this->namaBulan($bulan) . ' ' . $tahun;
        $sheetLabel = $result['sheet_name'] ? " (sheet: {$result['sheet_name']})" : '';
        $pesan      = "Berhasil menyimpan absensi {$berhasil} karyawan untuk {$label}{$sheetLabel}.";

        if ($periode && !$periode->isLocked()) {
            $pesan .= " Nilai Kehadiran & Kedisiplinan akan otomatis terisi sebagai pilihan default saat membuka form Input Nilai periode {$label} (tersimpan setelah Anda klik Simpan).";
        } elseif (!$periode) {
            $pesan .= " (Belum ada periode {$label} — data absensi tetap tersimpan.)";
        } elseif ($periode->isLocked()) {
            $pesan .= " (Periode {$label} sudah selesai/terkunci.)";
        }

        if (!empty($tidakDitemukan)) {
            $pesan .= ' Nama tidak cocok di database: ' . implode(', ', $tidakDitemukan) . '.';
        }
        if (!empty($result['warning'])) {
            $pesan .= ' ⚠ ' . $result['warning'];
        }

        return $redirectTarget->with('success', $pesan);
    }

    /**
     * Simpan kehadiran harian ke tabel absensi.
     *
     * Untuk tiap tanggal hari kerja di bulan tsb:
     *   - status 'hadir' jika karyawan tercatat hadir (sel = 1)
     *   - status 'alpha' jika hari kerja tapi sel kosong
     *
     * Strategi: hapus dulu data karyawan ini pada bulan & tahun tsb,
     * lalu insert ulang. Ini membuat import ulang selalu bersih
     * (idempoten) tanpa risiko duplikat / bentrok format tanggal.
     */
    private function simpanAbsensiHarian(int $karyawanId, int $bulan, int $tahun, array $workingDays, array $hadirDays, array $terlambatDays = []): void
    {
        // Bersihkan data lama karyawan ini pada bulan & tahun tsb
        Absensi::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->delete();

        $hadirSet = array_flip($hadirDays);     // lookup cepat O(1)
        $lateSet  = array_flip($terlambatDays);
        $rows     = [];

        foreach ($workingDays as $tgl) {
            // Lewati tanggal yang tidak valid untuk bulan ini (mis. 31 di Februari)
            if (!checkdate($bulan, $tgl, $tahun)) continue;

            $hadir = isset($hadirSet[$tgl]);
            $late  = $hadir && isset($lateSet[$tgl]);

            $rows[] = [
                'karyawan_id' => $karyawanId,
                'tanggal'     => Carbon::create($tahun, $bulan, $tgl)->toDateString(),
                'status'      => $hadir ? 'hadir' : 'alpha',
                'terlambat'   => $late,
                'keterangan'  => $hadir
                    ? ($late ? 'Terlambat (diimport dari Excel)' : null)
                    : 'Tidak hadir (diimport dari Excel)',
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        if (!empty($rows)) {
            Absensi::insert($rows);
        }
    }

    // ── Helper nama bulan Indonesia ──────────────────────────────

    private function namaBulan(int $bulan): string
    {
        $nama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $nama[$bulan] ?? (string) $bulan;
    }
}