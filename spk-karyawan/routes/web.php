<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\PenggunaController;

// ──────────────────────────────────────────
// AUTH (publik)
// ──────────────────────────────────────────
Route::get('/',        [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ──────────────────────────────────────────
// AUTHENTICATED
// ──────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard — semua role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil',     [ProfilController::class, 'show'])->name('profil.show');
    Route::get('/profil/edit',[ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil',     [ProfilController::class, 'update'])->name('profil.update');

    // ── PENGGUNA — admin & direktur ────────────────────
    Route::middleware('role:admin,direktur')->group(function () {
        Route::resource('pengguna', PenggunaController::class)->except(['show']);
    });

    // ── KARYAWAN — admin & direktur ────────────────────
    Route::middleware('role:admin,direktur')->group(function () {
        Route::resource('karyawan', KaryawanController::class)->except(['show']);
        Route::get('/karyawan/{karyawan}/akun',        [KaryawanController::class, 'akunForm'])->name('karyawan.akun.form');
        Route::post('/karyawan/{karyawan}/akun',       [KaryawanController::class, 'akunStore'])->name('karyawan.akun.store');
        Route::put('/karyawan/{karyawan}/akun',        [KaryawanController::class, 'akunUpdate'])->name('karyawan.akun.update');
    });

    // ── ABSENSI — admin & direktur ────────────────────
    Route::middleware('role:admin,direktur')->group(function () {
        // Rekap absensi (halaman utama menu Absensi) — grid kehadiran harian
        Route::get('/absensi', [AbsensiController::class, 'rekap'])->name('absensi.rekap');

        // Upload dari menu Absensi (pilih periode sendiri)
        Route::get('/absensi/upload',  [AbsensiController::class, 'uploadFormIndex'])->name('absensi.upload.index');
        Route::post('/absensi/upload', [AbsensiController::class, 'uploadProsesIndex'])->name('absensi.upload.proses-index');

        // Upload dari halaman Penilaian (periode sudah diketahui)
        Route::get('/periode/{periode}/absensi/upload',  [AbsensiController::class, 'uploadForm'])->name('absensi.upload.form');
        Route::post('/periode/{periode}/absensi/upload', [AbsensiController::class, 'uploadProses'])->name('absensi.upload.proses');
    });

    // ── KRITERIA & SUB-KRITERIA — hanya direktur ───────
    Route::middleware('role:direktur')->group(function () {
        Route::resource('kriteria', KriteriaController::class)->except(['show']);

        // Sub-kriteria dengan kriteria_id (dari tombol di halaman kriteria)
        Route::prefix('kriteria/{kriteria}/sub-kriteria')
            ->name('kriteria.sub-kriteria')
            ->group(function () {
                Route::get('/',                 [KriteriaController::class, 'subKriteriaIndex'])->name('');
                Route::post('/',                [KriteriaController::class, 'subKriteriaStore'])->name('.store');
                Route::put('/{subKriteria}',    [KriteriaController::class, 'subKriteriaUpdate'])->name('.update');
                Route::delete('/{subKriteria}', [KriteriaController::class, 'subKriteriaDestroy'])->name('.destroy');
            });

    });

    // Sub-Kriteria standalone — untuk menu sidebar (hanya direktur)
    Route::middleware('role:direktur')->get('/sub-kriteria', [KriteriaController::class, 'subKriteriaAll'])->name('sub-kriteria.index');

    // ── PERIODE & PENILAIAN — hanya direktur ───────────
    Route::middleware('role:direktur')->group(function () {
        Route::get('/periode',                       [PeriodeController::class, 'index'])->name('periode.index');
        Route::get('/periode/create',                [PeriodeController::class, 'create'])->name('periode.create');
        Route::post('/periode',                      [PeriodeController::class, 'store'])->name('periode.store');
        Route::get('/periode/{periode}',             [PeriodeController::class, 'show'])->name('periode.show');
        Route::post('/periode/{periode}/selesaikan', [PeriodeController::class, 'selesaikan'])->name('periode.selesaikan');

        Route::get('/periode/{periode}/penilaian',                    [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/periode/{periode}/penilaian/{karyawan}/form',    [PenilaianController::class, 'form'])->name('penilaian.form');
        Route::post('/periode/{periode}/penilaian/{karyawan}/simpan', [PenilaianController::class, 'simpan'])->name('penilaian.simpan');

        Route::post('/periode/{periode}/hitung', [RankingController::class, 'hitung'])->name('ranking.hitung');
    });

    // ── HASIL RANKING — hanya direktur ────────────────
    Route::middleware('role:direktur')->group(function () {
        Route::get('/ranking',                                                    [RankingController::class, 'index'])->name('ranking.index');
        Route::get('/periode/{periode}/ranking',                                  [RankingController::class, 'hasil'])->name('ranking.hasil');
        Route::get('/periode/{periode}/ranking/cetak',                            [RankingController::class, 'cetak'])->name('ranking.cetak');
        Route::get('/periode/{periode}/ranking/{karyawan}/edit-nilai',            [RankingController::class, 'editNilai'])->name('ranking.edit-nilai');
        Route::put('/periode/{periode}/ranking/{karyawan}/edit-nilai',            [RankingController::class, 'updateNilai'])->name('ranking.update-nilai');
    });

    // ── NILAI SAYA — admin & karyawan ──────────────────
    Route::middleware('role:admin,karyawan')->group(function () {
        Route::get('/hasil-penilaian', function () {
            $periodeSelesai = \App\Models\Periode::where('status', 'selesai')
                ->with(['hasilRanking.karyawan'])
                ->orderByDesc('tahun')
                ->orderByDesc('bulan')
                ->get();
            return view('ranking.publik', compact('periodeSelesai'));
        })->name('ranking.publik');

        Route::get('/nilai-saya', function () {
            $karyawan = Auth::user()->karyawan;
            if (!$karyawan) abort(403, 'Akun Anda tidak terhubung ke data karyawan.');
            $riwayat = \App\Models\HasilRanking::where('karyawan_id', $karyawan->id)
                ->with('periode')
                ->orderByDesc('id')
                ->get();
            $periodeSelesai = \App\Models\Periode::where('status', 'selesai')
                ->with(['hasilRanking.karyawan'])
                ->orderByDesc('tahun')->orderByDesc('bulan')
                ->get();
            return view('karyawan.nilai', compact('karyawan', 'riwayat', 'periodeSelesai'));
        })->name('karyawan.nilai');
    });
});