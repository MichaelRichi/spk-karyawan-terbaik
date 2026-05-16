<?php

namespace App\Services;

use App\Models\Kriteria;
use App\Models\Periode;
use App\Models\PeriodeKriteria;
use App\Models\PeriodeSubKriteria;
use Illuminate\Support\Facades\DB;

class PeriodeService
{
    public function buat(array $data): Periode
    {
        DB::beginTransaction();
        try {
            $periode = Periode::create($data);
            $this->salinSnapshot($periode);
            DB::commit();
            return $periode;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function salinSnapshot(Periode $periode): void
    {
        // Ambil semua kriteria beserta sub-kriterianya
        $kriteriaList = Kriteria::with('subKriteria')->get();

        if ($kriteriaList->isEmpty()) {
            throw new \Exception('Belum ada kriteria. Tambahkan kriteria terlebih dahulu.');
        }

        foreach ($kriteriaList as $k) {
            // Simpan snapshot kriteria
            $pk = PeriodeKriteria::create([
                'periode_id'    => $periode->id,
                'kriteria_id'   => $k->id,
                'nama_kriteria' => $k->nama,
                'jenis'         => $k->jenis,
                'bobot'         => $k->bobot_default,
            ]);

            // Simpan snapshot sub-kriteria
            foreach ($k->subKriteria as $sk) {
                PeriodeSubKriteria::create([
                    'periode_kriteria_id' => $pk->id,
                    'sub_kriteria_id'     => $sk->id,
                    'nama'                => $sk->nama,  // pakai nama bukan label
                    'skor'                => $sk->skor,
                ]);
            }
        }
    }

    public function selesaikan(Periode $periode): void
    {
        if ($periode->status !== 'aktif') {
            throw new \Exception('Hanya periode berstatus aktif yang dapat diselesaikan.');
        }

        if (!$periode->isBobotValid()) {
            $total = $periode->periodeKriteria()->sum('bobot');
            throw new \Exception("Total bobot harus 100%. Saat ini: {$total}%.");
        }

        if (!$periode->hasilRanking()->exists()) {
            throw new \Exception('Belum ada hasil SAW. Jalankan hitung SAW terlebih dahulu.');
        }

        $periode->update(['status' => 'selesai']);
    }

    public function sinkronSnapshot(Periode $periode): void
    {
        if ($periode->status === 'selesai') {
            throw new \Exception('Periode sudah selesai, tidak dapat disinkronkan.');
        }

        DB::beginTransaction();
        try {
            // Hapus snapshot lama
            $periodeKriteriaIds = $periode->periodeKriteria()->pluck('id');
            PeriodeSubKriteria::whereIn('periode_kriteria_id', $periodeKriteriaIds)->delete();
            PeriodeKriteria::where('periode_id', $periode->id)->delete();
            $this->salinSnapshot($periode);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}