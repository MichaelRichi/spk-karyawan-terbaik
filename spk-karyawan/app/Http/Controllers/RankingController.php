<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use App\Services\SawService;
use Illuminate\Support\Facades\Auth;

class RankingController extends Controller
{
    public function __construct(private SawService $sawService) {}

    /** Halaman index ranking — pilih periode */
    public function index()
    {
        $periode = Periode::whereIn('status', ['aktif', 'selesai'])
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

    /** Cetak laporan PDF */
    public function cetak(Periode $periode)
    {
        $detail          = $this->sawService->getDetailRanking($periode);
        $periodeKriteria = $periode->periodeKriteria()->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'ranking.cetak',
            compact('periode', 'detail', 'periodeKriteria')
        )->setPaper('a4', 'landscape');

        return $pdf->download("Ranking_Karyawan_{$periode->nama}.pdf");
    }
}