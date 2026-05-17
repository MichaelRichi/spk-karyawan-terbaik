<?php

namespace App\Http\Controllers;

use App\Models\HasilRanking;
use App\Models\Karyawan;
use App\Models\Periode;

class DashboardController extends Controller
{
    public function index()
    {
        // Total karyawan AKTIF saja
        $totalKaryawan  = Karyawan::aktif()->count();
        $totalPeriode   = Periode::where('status', 'selesai')->count();
        $periodeAktif   = Periode::where('status', 'aktif')->with('penilaian', 'periodeKriteria')->first();
        $riwayat        = Periode::with('hasilRanking.karyawan')->orderByDesc('id')->take(5)->get();
        $karyawanTerbaik = HasilRanking::where('ranking', 1)
            ->with('karyawan', 'periode')
            ->orderByDesc('id')
            ->first();

        return view('dashboard.index', compact(
            'totalKaryawan', 'totalPeriode', 'periodeAktif', 'riwayat', 'karyawanTerbaik'
        ));
    }
}