<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Periode;
use App\Models\HasilRanking;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = Karyawan::count();
        $totalPeriode  = Periode::count();

        $periodeAktif = Periode::where('status', 'aktif')->first();

        $periodeTerakhir = Periode::where('status', 'selesai')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();

        $karyawanTerbaik = null;
        if ($periodeTerakhir) {
            $karyawanTerbaik = HasilRanking::where('periode_id', $periodeTerakhir->id)
                ->where('ranking', 1)
                ->with('karyawan')
                ->first();
        }

        // Riwayat 6 bulan terakhir untuk tabel ringkasan
        $riwayat = Periode::where('status', 'selesai')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        return view('dashboard.index', compact(
            'totalKaryawan', 'totalPeriode', 'periodeAktif',
            'periodeTerakhir', 'karyawanTerbaik', 'riwayat'
        ));
    }
}