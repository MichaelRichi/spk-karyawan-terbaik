<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\PeriodeSubKriteria;
use App\Services\SawService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RankingController extends Controller
{
    public function __construct(private SawService $sawService) {}

    /** Halaman index ranking — pilih periode */
    public function index()
    {
        $periode = Periode::where('status', 'selesai')
            ->has('hasilRanking')
            ->with('hasilRanking.karyawan')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        return view('ranking.index', compact('periode'));
    }

    /** Jalankan perhitungan SAW */
    public function hitung(Periode $periode)
    {
        try {
            $this->sawService->hitung($periode);
            return redirect()->route('ranking.hasil', $periode)
                ->with('success', 'Perhitungan SAW berhasil! Hasil ranking sudah diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /** Tampilkan hasil ranking */
    public function hasil(Periode $periode)
    {
        if ($periode->status !== 'selesai') {
            return redirect()->route('ranking.index')
                ->with('error', 'Hasil ranking hanya tersedia untuk periode yang sudah selesai.');
        }

        $detail = $this->sawService->getDetailRanking($periode);

        // Karyawan hanya bisa lihat data dirinya sendiri
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if ($authUser->isKaryawan()) {
            $myId   = $authUser->karyawan_id;
            $detail = array_values(
                array_filter($detail, fn($d) => $d['karyawan']->id === $myId)
            );
        }

        $periodeKriteria = $periode->periodeKriteria()->get();

        return view('ranking.hasil', compact('periode', 'detail', 'periodeKriteria'));
    }

    /** Form edit nilai karyawan di periode selesai */
    public function editNilai(Periode $periode, Karyawan $karyawan)
    {
        $periodeKriteria = $periode->periodeKriteria()->with('periodeSubKriteria')->get();
        $nilaiExisting   = Penilaian::where('periode_id', $periode->id)
            ->where('karyawan_id', $karyawan->id)
            ->get()->keyBy('periode_kriteria_id');

        return view('ranking.edit-nilai', compact('periode', 'karyawan', 'periodeKriteria', 'nilaiExisting'));
    }

    /** Simpan perubahan nilai dan hitung ulang SAW */
    public function updateNilai(Request $request, Periode $periode, Karyawan $karyawan)
    {
        $request->validate(['penilaian' => 'required|array']);

        foreach ($request->penilaian as $pkId => $pskId) {
            $psk = PeriodeSubKriteria::findOrFail($pskId);
            Penilaian::updateOrCreate(
                ['periode_id' => $periode->id, 'karyawan_id' => $karyawan->id, 'periode_kriteria_id' => $pkId],
                ['periode_sub_kriteria_id' => $psk->id, 'nilai' => $psk->skor]
            );
        }

        // Hitung ulang SAW
        try {
            $this->sawService->hitungUlang($periode);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('ranking.hasil', $periode)
            ->with('success', "Nilai {$karyawan->nama} berhasil diperbarui dan ranking telah dihitung ulang.");
    }

    /** Cetak laporan PDF */
    public function cetak(Periode $periode)
    {
        if ($periode->status !== 'selesai') {
            return redirect()->route('ranking.index')
                ->with('error', 'Laporan hanya tersedia untuk periode yang sudah selesai.');
        }
        $detail          = $this->sawService->getDetailRanking($periode);
        $periodeKriteria = $periode->periodeKriteria()->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'ranking.cetak',
            compact('periode', 'detail', 'periodeKriteria')
        )->setPaper('a4', 'landscape');

        return $pdf->download("Ranking_Karyawan_{$periode->nama}.pdf");
    }
}