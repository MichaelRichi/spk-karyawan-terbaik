<?php

namespace App\Http\Controllers;

use App\Models\HasilRanking;
use App\Models\Karyawan;
use App\Models\Kriteria;
use App\Models\Periode;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // ── DIREKTUR ──────────────────────────────────
        if ($role === 'direktur') {
            $totalKaryawan   = Karyawan::aktif()->count();
            $totalPeriode    = Periode::where('status', 'selesai')->count();
            $totalKriteria   = Kriteria::count();
            $periodeAktif    = Periode::where('status', 'aktif')->with('penilaian')->first();

            // Periode selesai terakhir + Top 3 karyawan terbaik
            $periodeTerakhir = Periode::where('status', 'selesai')->orderByDesc('id')->first();
            $topKaryawan = $periodeTerakhir
                ? HasilRanking::where('periode_id', $periodeTerakhir->id)
                    ->with('karyawan')->orderBy('ranking')->take(5)->get()
                : collect();

            // Distribusi kriteria saat ini
            $kriteriaList = Kriteria::orderByDesc('bobot')->get();

            return view('dashboard.direktur', compact(
                'totalKaryawan','totalPeriode','totalKriteria','periodeAktif',
                'periodeTerakhir','topKaryawan','kriteriaList'
            ));
        }

        // ── ADMIN ─────────────────────────────────────
        if ($role === 'admin') {
            $totalKaryawan   = Karyawan::aktif()->count();
            $totalTidakAktif = Karyawan::where('status','tidak_aktif')->count();
            $periodeAktif    = Periode::where('status','aktif')->with('penilaian')->first();
            $totalDinilai    = $periodeAktif ? $periodeAktif->penilaian->pluck('karyawan_id')->unique()->count() : 0;
            // Data karyawan admin (jika terhubung)
            $karyawan      = $user->karyawan;
            $nilaiTerakhir = $karyawan
                ? HasilRanking::where('karyawan_id', $karyawan->id)->with('periode')->orderByDesc('id')->first()
                : null;
            $totalDinilaiSaya = $karyawan
                ? HasilRanking::where('karyawan_id', $karyawan->id)->count()
                : 0;
            return view('dashboard.admin', compact(
                'totalKaryawan','totalTidakAktif','periodeAktif','totalDinilai',
                'karyawan','nilaiTerakhir','totalDinilaiSaya'
            ));
        }

        // ── KARYAWAN ──────────────────────────────────
        $karyawan    = $user->karyawan;
        $nilaiTerakhir = $karyawan
            ? HasilRanking::where('karyawan_id', $karyawan->id)->with('periode')->orderByDesc('id')->first()
            : null;
        $totalDinilai = $karyawan
            ? HasilRanking::where('karyawan_id', $karyawan->id)->count()
            : 0;
        return view('dashboard.karyawan', compact('karyawan','nilaiTerakhir','totalDinilai'));
    }
}