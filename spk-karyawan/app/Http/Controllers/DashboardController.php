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
            $karyawanTetap      = Karyawan::aktif()->tipe('tetap')->count();
            $karyawanTidakTetap = Karyawan::aktif()->tipe('tidak_tetap')->count();
            $totalPeriode    = Periode::where('status', 'selesai')->count();
            $totalKriteria   = Kriteria::count();
            $kriteriaTetap      = Kriteria::where('tipe', 'tetap')->count();
            $kriteriaTidakTetap = Kriteria::where('tipe', 'tidak_tetap')->count();
            $periodeAktif    = Periode::where('status', 'aktif')->with('penilaian')->first();

            // Periode selesai terakhir (berdasarkan kalender: tahun & bulan terbaru)
            $periodeTerakhir = Periode::where('status', 'selesai')
                ->orderByDesc('tahun')->orderByDesc('bulan')->first();
            $topPerTipe = ['tetap' => collect(), 'tidak_tetap' => collect()];
            if ($periodeTerakhir) {
                foreach (['tetap', 'tidak_tetap'] as $tp) {
                    $topPerTipe[$tp] = HasilRanking::where('periode_id', $periodeTerakhir->id)
                        ->where('tipe', $tp)->with('karyawan')->orderBy('ranking')->take(5)->get();
                }
            }

            // Distribusi kriteria saat ini
            $kriteriaList = Kriteria::orderByDesc('bobot')->get();

            return view('dashboard.direktur', compact(
                'totalKaryawan','karyawanTetap','karyawanTidakTetap',
                'totalPeriode','totalKriteria','kriteriaTetap','kriteriaTidakTetap',
                'periodeAktif','periodeTerakhir','topPerTipe','kriteriaList'
            ));
        }

        // ── ADMIN ─────────────────────────────────────
        if ($role === 'admin') {
            $totalKaryawan   = Karyawan::aktif()->count();
            $karyawanTetap      = Karyawan::aktif()->tipe('tetap')->count();
            $karyawanTidakTetap = Karyawan::aktif()->tipe('tidak_tetap')->count();
            $totalTidakAktif = Karyawan::where('status','tidak_aktif')->count();
            $periodeAktif    = Periode::where('status','aktif')->with('penilaian')->first();
            $totalDinilai    = $periodeAktif ? $periodeAktif->penilaian->pluck('karyawan_id')->unique()->count() : 0;
            // Data karyawan admin (jika terhubung)
            $karyawan      = $user->karyawan;
            $nilaiTerakhir = $karyawan
                ? HasilRanking::where('hasil_ranking.karyawan_id', $karyawan->id)
                ->join('periode', 'hasil_ranking.periode_id', '=', 'periode.id')
                ->orderByDesc('periode.tahun')->orderByDesc('periode.bulan')
                ->select('hasil_ranking.*')->with('periode')->first()
                : null;
            $totalDinilaiSaya = $karyawan
                ? HasilRanking::where('karyawan_id', $karyawan->id)->count()
                : 0;
            return view('dashboard.admin', compact(
                'totalKaryawan','karyawanTetap','karyawanTidakTetap','totalTidakAktif','periodeAktif','totalDinilai',
                'karyawan','nilaiTerakhir','totalDinilaiSaya'
            ));
        }

        // ── KARYAWAN ──────────────────────────────────
        $karyawan    = $user->karyawan;
        $nilaiTerakhir = $karyawan
            ? HasilRanking::where('hasil_ranking.karyawan_id', $karyawan->id)
                ->join('periode', 'hasil_ranking.periode_id', '=', 'periode.id')
                ->orderByDesc('periode.tahun')->orderByDesc('periode.bulan')
                ->select('hasil_ranking.*')->with('periode')->first()
            : null;
        $totalDinilai = $karyawan
            ? HasilRanking::where('karyawan_id', $karyawan->id)->count()
            : 0;
        return view('dashboard.karyawan', compact('karyawan','nilaiTerakhir','totalDinilai'));
    }
}