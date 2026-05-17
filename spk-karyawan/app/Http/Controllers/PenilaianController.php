<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenilaianRequest;
use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\PeriodeSubKriteria;
use App\Models\HasilRanking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    public function index(Periode $periode)
    {
        if ($periode->status === 'draft') {
            return redirect()->route('periode.show', $periode)
                ->with('error', 'Aktifkan periode terlebih dahulu sebelum input penilaian.');
        }

        // Hanya karyawan AKTIF yang bisa dinilai
        $karyawan = Karyawan::aktif()->orderBy('nama')->get();

        $jumlahKriteria = $periode->periodeKriteria()->count();

        // ID karyawan yang sudah dinilai lengkap (semua kriteria)
        $penilaianSelesai = Penilaian::where('periode_id', $periode->id)
            ->select('karyawan_id', DB::raw('count(*) as jumlah'))
            ->groupBy('karyawan_id')
            ->having('jumlah', '>=', $jumlahKriteria)
            ->pluck('jumlah', 'karyawan_id')
            ->keys();

        // Nilai yang sudah diinput per karyawan per kriteria
        $nilaiKaryawan = [];
        $penilaianAll = Penilaian::where('periode_id', $periode->id)
            ->with('periodeSubKriteria')
            ->get();
        foreach ($penilaianAll as $p) {
            $nilaiKaryawan[$p->karyawan_id][$p->periode_kriteria_id] = $p;
        }

        return view('penilaian.index', compact(
            'periode', 'karyawan', 'penilaianSelesai', 'jumlahKriteria', 'nilaiKaryawan'
        ));
    }

    public function form(Periode $periode, Karyawan $karyawan)
    {
        // Cek karyawan aktif
        if (!$karyawan->isAktif()) {
            return redirect()->route('penilaian.index', $periode)
                ->with('error', "Karyawan {$karyawan->nama} tidak aktif dan tidak dapat dinilai.");
        }

        $periodeKriteria = $periode->periodeKriteria()
            ->with('periodeSubKriteria')
            ->get();

        // Nilai yang sudah tersimpan (untuk mode edit)
        $nilaiExisting = Penilaian::where('periode_id', $periode->id)
            ->where('karyawan_id', $karyawan->id)
            ->get()
            ->keyBy('periode_kriteria_id');

        return view('penilaian.form', compact(
            'periode', 'karyawan', 'periodeKriteria', 'nilaiExisting'
        ));
    }

    public function simpan(StorePenilaianRequest $request, Periode $periode, Karyawan $karyawan)
    {
        if (!$karyawan->isAktif()) {
            return back()->with('error', "Karyawan {$karyawan->nama} tidak aktif.");
        }

        DB::beginTransaction();
        try {
            foreach ($request->input('penilaian') as $pkId => $pskId) {
                $psk = PeriodeSubKriteria::findOrFail($pskId);

                Penilaian::updateOrCreate(
                    [
                        'periode_id'          => $periode->id,
                        'karyawan_id'         => $karyawan->id,
                        'periode_kriteria_id' => (int) $pkId,
                    ],
                    [
                        'periode_sub_kriteria_id' => $psk->id,
                        'nilai'                   => $psk->skor,
                        'nilai_normalisasi'        => 0,
                        'nilai_terbobot'           => 0,
                    ]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }

        return redirect()->route('penilaian.index', $periode)
            ->with('success', "Penilaian {$karyawan->nama} berhasil disimpan.");
    }
}