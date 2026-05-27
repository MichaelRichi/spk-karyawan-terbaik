<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Pengguna ──────────────────────────────────
        User::create([
            'username' => 'direktur',
            'password' => Hash::make('password'),
            'role'     => 'direktur',
        ]);

        // ── Kriteria & Sub-Kriteria ───────────────────
        $kriteriaData = [
            ['nama' => 'Kehadiran',      'jenis' => 'benefit', 'bobot' => 30],
            ['nama' => 'Masa Kerja',     'jenis' => 'benefit', 'bobot' => 15],
            ['nama' => 'Kedisiplinan',   'jenis' => 'cost',    'bobot' => 20],
            ['nama' => 'Tanggung Jawab', 'jenis' => 'benefit', 'bobot' => 20],
            ['nama' => 'Komunikasi',     'jenis' => 'benefit', 'bobot' => 15],
        ];

        $subKriteriaData = [
            'Kehadiran' => [
                ['skor' => 5, 'nama' => '≥ 26 hari kerja'],
                ['skor' => 4, 'nama' => '23–25 hari kerja'],
                ['skor' => 3, 'nama' => '20–22 hari kerja'],
                ['skor' => 2, 'nama' => '17–19 hari kerja'],
                ['skor' => 1, 'nama' => '< 17 hari kerja'],
            ],
            'Masa Kerja' => [
                ['skor' => 5, 'nama' => '> 10 tahun'],
                ['skor' => 4, 'nama' => '> 5 – 10 tahun'],
                ['skor' => 3, 'nama' => '> 3 – 5 tahun'],
                ['skor' => 2, 'nama' => '1 – 3 tahun'],
                ['skor' => 1, 'nama' => '< 1 tahun'],
            ],
            'Kedisiplinan' => [
                ['skor' => 1, 'nama' => 'Tidak pernah terlambat'],
                ['skor' => 2, 'nama' => '1 kali terlambat'],
                ['skor' => 3, 'nama' => '2 kali terlambat'],
                ['skor' => 4, 'nama' => '3 kali terlambat'],
                ['skor' => 5, 'nama' => '> 3 kali terlambat'],
            ],
            'Tanggung Jawab' => [
                ['skor' => 5, 'nama' => 'Sangat bagus'],
                ['skor' => 4, 'nama' => 'Bagus'],
                ['skor' => 3, 'nama' => 'Cukup'],
                ['skor' => 2, 'nama' => 'Kurang'],
                ['skor' => 1, 'nama' => 'Sangat kurang'],
            ],
            'Komunikasi' => [
                ['skor' => 5, 'nama' => 'Sangat bagus'],
                ['skor' => 4, 'nama' => 'Bagus'],
                ['skor' => 3, 'nama' => 'Cukup'],
                ['skor' => 2, 'nama' => 'Kurang'],
                ['skor' => 1, 'nama' => 'Sangat kurang'],
            ],
        ];

        foreach ($kriteriaData as $kd) {
            $k = Kriteria::create($kd);
            foreach ($subKriteriaData[$kd['nama']] as $sk) {
                SubKriteria::create([
                    'kriteria_id' => $k->id,
                    'nama'        => $sk['nama'],
                    'skor'        => $sk['skor'],
                ]);
            }
        }
    }
}