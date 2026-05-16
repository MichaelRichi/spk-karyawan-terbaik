<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. KARYAWAN (harus sebelum users karena FK) ──────────────
        $karyawan = [
            ['nama' => 'Paidi', 'jabatan' => 'Staff Lapangan', 'jenis_kelamin' => 'laki-laki',   'tanggal_masuk' => '2010-01-15', 'status' => 'tetap'],
            ['nama' => 'Heru',  'jabatan' => 'Staff Lapangan', 'jenis_kelamin' => 'laki-laki',   'tanggal_masuk' => '2012-03-10', 'status' => 'tetap'],
            ['nama' => 'Rizki', 'jabatan' => 'Teknisi',        'jenis_kelamin' => 'laki-laki',   'tanggal_masuk' => '2019-07-01', 'status' => 'tetap'],
            ['nama' => 'Dawi',  'jabatan' => 'Teknisi',        'jenis_kelamin' => 'laki-laki',   'tanggal_masuk' => '2022-02-01', 'status' => 'tidak_tetap'],
            ['nama' => 'Darso', 'jabatan' => 'Staff Lapangan', 'jenis_kelamin' => 'laki-laki',   'tanggal_masuk' => '2023-06-01', 'status' => 'tidak_tetap'],
        ];
        foreach ($karyawan as $k) {
            DB::table('karyawan')->insert(array_merge($k, [
                'created_at' => now(), 'updated_at' => now(),
            ]));
        }

        // ── 2. USERS ──────────────────────────────────────────────────
        DB::table('users')->insert([
            ['username' => 'admin',    'password' => Hash::make('password'), 'role' => 'admin',    'karyawan_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['username' => 'direktur', 'password' => Hash::make('password'), 'role' => 'direktur', 'karyawan_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 3. KRITERIA ───────────────────────────────────────────────
        $kriteriaList = [
            ['nama' => 'Kehadiran',      'jenis' => 'benefit', 'bobot_default' => 30],
            ['nama' => 'Masa Kerja',     'jenis' => 'benefit', 'bobot_default' => 15],
            ['nama' => 'Kedisiplinan',   'jenis' => 'cost',    'bobot_default' => 20],
            ['nama' => 'Tanggung Jawab', 'jenis' => 'benefit', 'bobot_default' => 20],
            ['nama' => 'Komunikasi',     'jenis' => 'benefit', 'bobot_default' => 15],
        ];
        foreach ($kriteriaList as $k) {
            DB::table('kriteria')->insert(array_merge($k, [
                'created_at' => now(), 'updated_at' => now(),
            ]));
        }

        // ── 4. SUB_KRITERIA ───────────────────────────────────────────
        $subKriteria = [
            // C1 - Kehadiran
            1 => [
                ['nama' => '>= 26 hari',   'skor' => 5],
                ['nama' => '23 - 25 hari', 'skor' => 4],
                ['nama' => '20 - 22 hari', 'skor' => 3],
                ['nama' => '17 - 19 hari', 'skor' => 2],
                ['nama' => '< 17 hari',    'skor' => 1],
            ],
            // C2 - Masa Kerja
            2 => [
                ['nama' => '> 10 tahun',   'skor' => 5],
                ['nama' => '5 - 10 tahun', 'skor' => 4],
                ['nama' => '3 - 5 tahun',  'skor' => 3],
                ['nama' => '1 - 3 tahun',  'skor' => 2],
                ['nama' => '< 1 tahun',    'skor' => 1],
            ],
            // C3 - Kedisiplinan (Cost)
            3 => [
                ['nama' => 'Tidak Pernah Terlambat', 'skor' => 1],
                ['nama' => '1 Kali Terlambat',       'skor' => 2],
                ['nama' => '2 Kali Terlambat',       'skor' => 3],
                ['nama' => '3 Kali Terlambat',       'skor' => 4],
                ['nama' => '> 3 Kali Terlambat',     'skor' => 5],
            ],
            // C4 - Tanggung Jawab
            4 => [
                ['nama' => 'Sangat Bagus',  'skor' => 5],
                ['nama' => 'Bagus',         'skor' => 4],
                ['nama' => 'Cukup',         'skor' => 3],
                ['nama' => 'Kurang',        'skor' => 2],
                ['nama' => 'Sangat Kurang', 'skor' => 1],
            ],
            // C5 - Komunikasi
            5 => [
                ['nama' => 'Sangat Bagus',  'skor' => 5],
                ['nama' => 'Bagus',         'skor' => 4],
                ['nama' => 'Cukup',         'skor' => 3],
                ['nama' => 'Kurang',        'skor' => 2],
                ['nama' => 'Sangat Kurang', 'skor' => 1],
            ],
        ];

        foreach ($subKriteria as $kriteriaId => $items) {
            foreach ($items as $item) {
                DB::table('sub_kriteria')->insert(array_merge($item, [
                    'kriteria_id' => $kriteriaId,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]));
            }
        }
    }
}