<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenilaianRequest;
use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\Absensi;
use App\Models\PeriodeSubKriteria;
use App\Models\HasilRanking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    public function index(Periode $periode, Request $request)
    {
        if ($periode->status === 'draft') {
            return redirect()->route('periode.show', $periode)
                ->with('error', 'Aktifkan periode terlebih dahulu sebelum input penilaian.');
        }

        if ($periode->status === 'selesai') {
            return redirect()->route('ranking.hasil', $periode)
                ->with('info', 'Periode ini sudah selesai. Berikut hasil rankingnya.');
        }

        // Tab tipe kepegawaian yang dinilai (default: tetap)
        $tipe = $request->input('tipe') === 'tidak_tetap' ? 'tidak_tetap' : 'tetap';

        // Karyawan AKTIF sesuai tipe tab
        $karyawan = Karyawan::aktif()->tipe($tipe)->orderBy('id')->get();

        // Kriteria snapshot untuk tipe ini (kolom tabel)
        $periodeKriteria = $periode->periodeKriteria()->where('tipe', $tipe)->get();
        $jumlahKriteria  = $periodeKriteria->count();

        $karyawanIds = $karyawan->pluck('id');

        // ID karyawan yang sudah dinilai lengkap (semua kriteria tipe ini)
        $penilaianSelesai = Penilaian::where('periode_id', $periode->id)
            ->whereIn('karyawan_id', $karyawanIds)
            ->select('karyawan_id', DB::raw('count(*) as jumlah'))
            ->groupBy('karyawan_id')
            ->having('jumlah', '>=', max($jumlahKriteria, 1))
            ->pluck('jumlah', 'karyawan_id')
            ->keys();

        // Nilai tersimpan per karyawan per kriteria
        $nilaiKaryawan = [];
        $penilaianAll = Penilaian::where('periode_id', $periode->id)
            ->whereIn('karyawan_id', $karyawanIds)
            ->with('periodeSubKriteria')
            ->get();
        foreach ($penilaianAll as $p) {
            $nilaiKaryawan[$p->karyawan_id][$p->periode_kriteria_id] = $p;
        }

        // Ringkasan jumlah & progres untuk kedua tab
        $ringkasanTab = [];
        foreach (Periode::tipeList() as $t) {
            $jmlK   = Karyawan::aktif()->tipe($t)->count();
            $jmlKr  = $periode->periodeKriteria()->where('tipe', $t)->count();
            $idsT   = Karyawan::aktif()->tipe($t)->pluck('id');
            $selesai = Penilaian::where('periode_id', $periode->id)
                ->whereIn('karyawan_id', $idsT)
                ->select('karyawan_id', DB::raw('count(*) as jumlah'))
                ->groupBy('karyawan_id')
                ->having('jumlah', '>=', max($jmlKr, 1))
                ->get()->count();
            $ringkasanTab[$t] = [
                'total'   => $jmlK,
                'selesai' => $selesai,
                'dihitung'=> $periode->sudahDihitung($t),
            ];
        }

        return view('penilaian.index', compact(
            'periode', 'karyawan', 'penilaianSelesai', 'jumlahKriteria',
            'nilaiKaryawan', 'periodeKriteria', 'tipe', 'ringkasanTab'
        ));
    }

    public function form(Periode $periode, Karyawan $karyawan)
    {
        if ($periode->status === 'selesai') {
            return redirect()->route('ranking.hasil', $periode)
                ->with('info', 'Periode ini sudah selesai dan terkunci.');
        }

        // Cek karyawan aktif
        if (!$karyawan->isAktif()) {
            return redirect()->route('penilaian.index', $periode)
                ->with('error', "Karyawan {$karyawan->nama} tidak aktif dan tidak dapat dinilai.");
        }

        // Kriteria snapshot sesuai tipe kepegawaian karyawan ini
        $periodeKriteria = $periode->periodeKriteria()
            ->where('tipe', $karyawan->tipe)
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
        if ($periode->status === 'selesai') {
            return back()->with('error', 'Periode ini sudah selesai dan terkunci. Nilai tidak dapat diubah.');
        }

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

        return redirect()->route('penilaian.index', ['periode' => $periode->id, 'tipe' => $karyawan->tipe])
            ->with('success', "Penilaian {$karyawan->nama} berhasil disimpan.");
    }
}