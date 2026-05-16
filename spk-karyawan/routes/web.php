<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\PeriodeController;
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

    // ── PENGGUNA — admin & direktur ────────────────────
    Route::middleware('role:admin,direktur')->group(function () {
        Route::resource('pengguna', PenggunaController::class)->except(['show']);
    });

    // ── KARYAWAN — admin & direktur ────────────────────
    Route::middleware('role:admin,direktur')->group(function () {
        Route::resource('karyawan', KaryawanController::class)->except(['show']);
    });

    // ── KRITERIA & SUB-KRITERIA — hanya direktur ───────
    Route::middleware('role:direktur')->group(function () {
        Route::resource('kriteria', KriteriaController::class)->except(['show']);

        Route::prefix('kriteria/{kriteria}/sub-kriteria')
            ->name('kriteria.sub-kriteria')
            ->group(function () {
                Route::get('/',                 [KriteriaController::class, 'subKriteriaIndex'])->name('');
                Route::post('/',                [KriteriaController::class, 'subKriteriaStore'])->name('.store');
                Route::put('/{subKriteria}',    [KriteriaController::class, 'subKriteriaUpdate'])->name('.update');
                Route::delete('/{subKriteria}', [KriteriaController::class, 'subKriteriaDestroy'])->name('.destroy');
            });
    });

    // ── PERIODE & PENILAIAN — hanya direktur ───────────
    Route::middleware('role:direktur')->group(function () {
        Route::get('/periode',                       [PeriodeController::class, 'index'])->name('periode.index');
        Route::get('/periode/create',                [PeriodeController::class, 'create'])->name('periode.create');
        Route::post('/periode',                      [PeriodeController::class, 'store'])->name('periode.store');
        Route::get('/periode/{periode}',             [PeriodeController::class, 'show'])->name('periode.show');
        Route::get('/periode/{periode}/bobot',       [PeriodeController::class, 'bobot'])->name('periode.bobot');
        Route::put('/periode/{periode}/bobot',       [PeriodeController::class, 'updateBobot'])->name('periode.bobot.update');
        Route::post('/periode/{periode}/aktifkan',   [PeriodeController::class, 'aktifkan'])->name('periode.aktifkan');
        Route::post('/periode/{periode}/selesaikan', [PeriodeController::class, 'selesaikan'])->name('periode.selesaikan');

        Route::get('/periode/{periode}/penilaian',                    [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('/periode/{periode}/penilaian/{karyawan}/form',    [PenilaianController::class, 'form'])->name('penilaian.form');
        Route::post('/periode/{periode}/penilaian/{karyawan}/simpan', [PenilaianController::class, 'simpan'])->name('penilaian.simpan');

        Route::post('/periode/{periode}/hitung', [RankingController::class, 'hitung'])->name('ranking.hitung');
    });

    // ── HASIL RANKING — admin, direktur, karyawan ──────
    Route::get('/ranking',                         [RankingController::class, 'index'])->name('ranking.index');
    Route::get('/periode/{periode}/ranking',       [RankingController::class, 'hasil'])->name('ranking.hasil');
    Route::get('/periode/{periode}/ranking/cetak', [RankingController::class, 'cetak'])->name('ranking.cetak');

    // ── NILAI SAYA — admin & karyawan ──────────────────
    Route::middleware('role:admin,karyawan')->group(function () {
        Route::get('/nilai-saya', function () {
            $karyawan = Auth::user()->karyawan;
            if (!$karyawan) abort(404, 'Data karyawan tidak ditemukan.');
            $riwayat = \App\Models\HasilRanking::where('karyawan_id', $karyawan->id)
                ->with('periode')
                ->orderByDesc('id')
                ->get();
            return view('karyawan.nilai', compact('karyawan', 'riwayat'));
        })->name('karyawan.nilai');
    });
});