<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Urutan truncate: dari tabel paling bawah ke paling atas
        DB::table('penilaian')->truncate();
        DB::table('hasil_ranking')->truncate();
        DB::table('periode_sub_kriteria')->truncate();
        DB::table('periode_kriteria')->truncate();
        DB::table('periode')->truncate();
        DB::table('sub_kriteria')->truncate();
        DB::table('kriteria')->truncate();
        DB::table('karyawan')->truncate();
        DB::table('users')->truncate();

        Schema::enableForeignKeyConstraints();

        // ── USER ─────────────────────────────────────────────────────
        // Hanya akun direktur yang dibuat agar sistem dapat diakses.
        \App\Models\User::create([
            'username' => 'direktur',
            'password' => Hash::make('password'),
            'role'     => 'direktur',
        ]);

        // ── DATA MASTER ───────────────────────────────────────────────
        // Karyawan, kriteria, dan sub-kriteria TIDAK dibuat di seeder.
        // → Direktur menginput sendiri melalui menu Kelola Karyawan,
        //   Kelola Kriteria, dan Kelola Sub-Kriteria sesuai kondisi
        //   PT Cempaka Indah Abadi.
        // → Periode dibuat lewat menu Periode setelah kedua set kriteria
        //   (tetap & tidak tetap) masing-masing bertotal 100%.
    }
}