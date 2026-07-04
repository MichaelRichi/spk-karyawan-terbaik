<?php

namespace App\Services;

use App\Models\HasilRanking;
use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;

class SawService
{
    /**
     * Jalankan perhitungan SAW untuk satu periode pada SATU tipe kepegawaian
     * (tetap / tidak_tetap). Pemisahan terjadi di dalam periode.
     *
     * Rumus:
     *   Benefit : Rij = Xij / max(Xij)
     *   Cost    : Rij = min(Xij) / Xij
     *   Vi      = Σ (Wj × Rij)
     */
    public function hitungUlang(Periode $periode, string $tipe): array
    {
        return $this->prosesHitung($periode, $tipe);
    }

    public function hitung(Periode $periode, string $tipe): array
    {
        return $this->prosesHitung($periode, $tipe);
    }

    private function prosesHitung(Periode $periode, string $tipe): array
    {
        $label = Periode::tipeLabel($tipe);

        $periodeKriteria = $periode->periodeKriteria()->where('tipe', $tipe)->get();
        if ($periodeKriteria->isEmpty()) {
            throw new \Exception("Belum ada kriteria {$label} untuk periode ini.");
        }

        // Karyawan aktif sesuai tipe
        $karyawanIds = Karyawan::aktif()->tipe($tipe)->pluck('id');
        if ($karyawanIds->isEmpty()) {
            throw new \Exception("Tidak ada karyawan {$label} yang dapat dihitung.");
        }

        $semuaPenilaian = Penilaian::where('periode_id', $periode->id)
            ->whereIn('karyawan_id', $karyawanIds)
            ->get();

        if ($semuaPenilaian->isEmpty()) {
            throw new \Exception("Belum ada data penilaian {$label} yang diinput.");
        }

        // Kelompokkan nilai per kriteria (snapshot tipe ini) untuk max/min
        $nilaiPerKriteria = [];
        foreach ($semuaPenilaian as $p) {
            $nilaiPerKriteria[$p->periode_kriteria_id][] = (float) $p->nilai;
        }

        DB::beginTransaction();
        try {
            // STEP 1 & 2: Normalisasi
            foreach ($semuaPenilaian as $p) { /** @var Penilaian $p */
                $pk = $periodeKriteria->firstWhere('id', $p->periode_kriteria_id);
                if (!$pk) { continue; } // pengaman: nilai di luar tipe ini
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

            // STEP 3: Vi = Σ(nilai_terbobot) per karyawan (hanya kriteria tipe ini)
            $pkIds = $periodeKriteria->pluck('id');
            $rankingData = [];
            foreach ($karyawanIds as $karyawanId) {
                $vi = $semuaPenilaian
                    ->where('karyawan_id', $karyawanId)
                    ->whereIn('periode_kriteria_id', $pkIds)
                    ->sum('nilai_terbobot');

                $rankingData[] = [
                    'karyawan_id'      => $karyawanId,
                    'nilai_preferensi' => round((float) $vi, 6),
                ];
            }

            // STEP 4: Urutkan Vi tertinggi = ranking 1
            usort($rankingData, fn($a, $b) => $b['nilai_preferensi'] <=> $a['nilai_preferensi']);

            // STEP 5: Simpan hasil ranking tipe ini (ganti yang lama untuk tipe ini saja)
            HasilRanking::where('periode_id', $periode->id)->where('tipe', $tipe)->delete();
            foreach ($rankingData as $rank => $data) {
                HasilRanking::create([
                    'periode_id'       => $periode->id,
                    'karyawan_id'      => $data['karyawan_id'],
                    'tipe'             => $tipe,
                    'nilai_preferensi' => $data['nilai_preferensi'],
                    'ranking'          => $rank + 1,
                ]);
            }

            // STEP 6: Periode dianggap selesai bila SEMUA tipe yang memiliki
            // karyawan sudah dihitung rankingnya.
            $tipeBerkaryawan = collect(Periode::tipeList())
                ->filter(fn($t) => Karyawan::aktif()->tipe($t)->exists());
            $semuaSelesai = $tipeBerkaryawan->every(
                fn($t) => $periode->hasilRanking()->where('tipe', $t)->exists()
            );
            $periode->update(['status' => $semuaSelesai ? 'selesai' : 'aktif']);

            DB::commit();
            return $rankingData;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Detail ranking + breakdown per kriteria, untuk satu tipe.
     */
    public function getDetailRanking(Periode $periode, string $tipe): array
    {
        $ranking = HasilRanking::where('periode_id', $periode->id)
            ->where('tipe', $tipe)
            ->with('karyawan')
            ->orderBy('ranking')
            ->get();

        $pkIds = $periode->periodeKriteria()->where('tipe', $tipe)->pluck('id');

        $result = [];
        foreach ($ranking as $r) {
            $detailPenilaian = Penilaian::where('periode_id', $periode->id)
                ->where('karyawan_id', $r->karyawan_id)
                ->whereIn('periode_kriteria_id', $pkIds)
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