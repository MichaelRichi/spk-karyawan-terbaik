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
            ['nama' => 'Kehadiran',      'jenis' => 'benefit', 'bobot' => 30, 'has_rentang' => true,  'satuan_rentang' => 'hari'],
            ['nama' => 'Masa Kerja',     'jenis' => 'benefit', 'bobot' => 15, 'has_rentang' => true,  'satuan_rentang' => 'tahun'],
            ['nama' => 'Kedisiplinan',   'jenis' => 'cost',    'bobot' => 20, 'has_rentang' => false, 'satuan_rentang' => null],
            ['nama' => 'Tanggung Jawab', 'jenis' => 'benefit', 'bobot' => 20, 'has_rentang' => false, 'satuan_rentang' => null],
            ['nama' => 'Komunikasi',     'jenis' => 'benefit', 'bobot' => 15, 'has_rentang' => false, 'satuan_rentang' => null],
        ];

        $subKriteriaData = [
            'Kehadiran' => [
                ['skor' => 5, 'nama' => '≥ 26 hari',    'nilai_min' => 26,  'nilai_max' => 99],
                ['skor' => 4, 'nama' => '23 – 25 hari',  'nilai_min' => 23,  'nilai_max' => 25],
                ['skor' => 3, 'nama' => '20 – 22 hari',  'nilai_min' => 20,  'nilai_max' => 22],
                ['skor' => 2, 'nama' => '17 – 19 hari',  'nilai_min' => 17,  'nilai_max' => 19],
                ['skor' => 1, 'nama' => '< 17 hari',     'nilai_min' => 0,   'nilai_max' => 16],
            ],
            'Masa Kerja' => [
                ['skor' => 5, 'nama' => '> 10 tahun',      'nilai_min' => 10.01, 'nilai_max' => 99],
                ['skor' => 4, 'nama' => '> 5 – 10 tahun',  'nilai_min' => 5.01,  'nilai_max' => 10],
                ['skor' => 3, 'nama' => '> 3 – 5 tahun',   'nilai_min' => 3.01,  'nilai_max' => 5],
                ['skor' => 2, 'nama' => '1 – 3 tahun',     'nilai_min' => 1,     'nilai_max' => 3],
                ['skor' => 1, 'nama' => '< 1 tahun',       'nilai_min' => 0,     'nilai_max' => 0],
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
                    'nilai_min'   => $sk['nilai_min'] ?? null,
                    'nilai_max'   => $sk['nilai_max'] ?? null,
                ]);
            }
        }
    }
}