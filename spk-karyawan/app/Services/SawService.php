<?php

namespace App\Services;

use App\Models\HasilRanking;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;

class SawService
{
    /**
     * Jalankan perhitungan SAW untuk satu periode.
     *
     * Rumus:
     *   Benefit : Rij = Xij / max(Xij)
     *   Cost    : Rij = min(Xij) / Xij
     *   Vi      = Σ (Wj × Rij)
     */
    public function hitungUlang(Periode $periode): array
    {
        // Hitung ulang tanpa cek isLocked — untuk koreksi nilai
        return $this->prosesHitung($periode);
    }

    public function hitung(Periode $periode): array
    {
        if ($periode->isLocked()) {
            throw new \Exception('Periode sudah selesai dan tidak dapat dihitung ulang.');
        }

        return $this->prosesHitung($periode);
    }

    private function prosesHitung(Periode $periode): array
    {
        $periodeKriteria = $periode->periodeKriteria()->get();

        if ($periodeKriteria->isEmpty()) {
            throw new \Exception('Belum ada kriteria yang dikonfigurasi untuk periode ini.');
        }

        $semuaPenilaian = Penilaian::where('periode_id', $periode->id)->get();

        if ($semuaPenilaian->isEmpty()) {
            throw new \Exception('Belum ada data penilaian yang diinput.');
        }

        // Kelompokkan nilai per kriteria untuk mencari max / min
        $nilaiPerKriteria = [];
        foreach ($semuaPenilaian as $p) {
            $nilaiPerKriteria[$p->periode_kriteria_id][] = (float) $p->nilai;
        }

        DB::beginTransaction();
        try {
            // STEP 1 & 2: Normalisasi
            foreach ($semuaPenilaian as $p) { /** @var \App\Models\Penilaian $p */
                $pk    = $periodeKriteria->firstWhere('id', $p->periode_kriteria_id);
                $nilai = (float) $p->nilai;
                $kolom = $nilaiPerKriteria[$p->periode_kriteria_id] ?? [$nilai];

                if ($pk->jenis === 'benefit') {
                    $maks = max($kolom);
                    $rij  = $maks > 0 ? ($nilai / $maks) : 0;
                } else {
                    $min = min($kolom);
                    $rij = $nilai > 0 ? ($min / $nilai) : 0;
                }

                $wj       = (float) $pk->bobot / 100;
                $terbobot = round($rij * $wj, 6);

                $p->update([
                    'nilai_normalisasi' => round($rij, 6),
                    'nilai_terbobot'    => $terbobot,
                ]);
            }

            // STEP 3: Vi = Σ(nilai_terbobot) per karyawan
            $karyawanIds = $semuaPenilaian->pluck('karyawan_id')->unique();
            $rankingData = [];

            foreach ($karyawanIds as $karyawanId) {
                $vi = $semuaPenilaian
                    ->where('karyawan_id', $karyawanId)
                    ->sum('nilai_terbobot');

                $rankingData[] = [
                    'karyawan_id'      => $karyawanId,
                    'nilai_preferensi' => round((float) $vi, 6),
                ];
            }

            // STEP 4: Urutkan Vi tertinggi = ranking 1
            usort($rankingData, fn($a, $b) => $b['nilai_preferensi'] <=> $a['nilai_preferensi']);

            // STEP 5: Simpan hasil ranking
            HasilRanking::where('periode_id', $periode->id)->delete();

            foreach ($rankingData as $rank => $data) {
                HasilRanking::create([
                    'periode_id'       => $periode->id,
                    'karyawan_id'      => $data['karyawan_id'],
                    'nilai_preferensi' => $data['nilai_preferensi'],
                    'ranking'          => $rank + 1,
                ]);
            }

            // STEP 6: Set status selesai
            if ($periode->status !== 'selesai') {
                $periode->update(['status' => 'selesai']);
            }

            DB::commit();
            return $rankingData;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Ambil detail ranking lengkap dengan breakdown per kriteria.
     */
    public function getDetailRanking(Periode $periode): array
    {
        $ranking = HasilRanking::where('periode_id', $periode->id)
            ->with('karyawan')
            ->orderBy('ranking')
            ->get();

        $result = [];
        foreach ($ranking as $r) {
            $detailPenilaian = Penilaian::where('periode_id', $periode->id)
                ->where('karyawan_id', $r->karyawan_id)
                ->with(['periodeKriteria', 'periodeSubKriteria'])
                ->get();

            $result[] = [
                'ranking'          => $r->ranking,
                'karyawan'         => $r->karyawan,
                'nilai_preferensi' => $r->nilai_preferensi,
                'detail_kriteria'  => $detailPenilaian->map(fn($p) => [
                    'nama_kriteria'     => $p->periodeKriteria->nama_kriteria,
                    'jenis'             => $p->periodeKriteria->jenis,
                    'bobot_persen'      => $p->periodeKriteria->bobot,
                    'bobot_desimal'     => round($p->periodeKriteria->bobot / 100, 4),
                    'nama_nilai'        => optional($p->periodeSubKriteria)->nama ?? '—',
                    'nilai'             => $p->nilai,
                    'nilai_normalisasi' => $p->nilai_normalisasi,
                    'nilai_terbobot'    => $p->nilai_terbobot,
                ]),
            ];
        }

        return $result;
    }
}