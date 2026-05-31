<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\Periode;
use App\Models\PeriodeKriteria;
use App\Models\PeriodeSubKriteria;

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
        $direktur = \App\Models\User::create([
            'username' => 'direktur',
            'password' => Hash::make('password'),
            'role'     => 'direktur',
        ]);

        // ── KARYAWAN ──────────────────────────────────────────────────
        $dataKaryawan = [
            ['nama' => 'PAIDI',         'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2016-03-28'],
            ['nama' => 'HERU',          'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2019-07-17'],
            ['nama' => 'RIZKI',         'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2023-02-03'],
            ['nama' => 'DAWI',          'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2025-07-15'],
            ['nama' => 'SELAMET SUPIR', 'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2024-10-24'],
            ['nama' => 'BOBO',          'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2025-05-12'],
            ['nama' => 'DARSO',         'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2024-09-26'],
            ['nama' => 'JUMADI',        'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2018-06-07'],
            ['nama' => 'RIKO B',        'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2018-07-27'],
            ['nama' => 'RIA',           'jenis_kelamin' => 'Perempuan',  'tgl_masuk' => '2021-02-28'],
            ['nama' => 'NADYA',         'jenis_kelamin' => 'Perempuan',  'tgl_masuk' => '2019-07-01'],
            ['nama' => 'NINI',          'jenis_kelamin' => 'Perempuan',  'tgl_masuk' => '2019-10-31'],
            ['nama' => 'YULI',          'jenis_kelamin' => 'Perempuan',  'tgl_masuk' => '2016-05-21'],
            ['nama' => 'TASYA',         'jenis_kelamin' => 'Perempuan',  'tgl_masuk' => '2018-12-11'],
            ['nama' => 'RARA',          'jenis_kelamin' => 'Perempuan',  'tgl_masuk' => '2017-04-08'],
            ['nama' => 'LOKI',          'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2018-08-03'],
            ['nama' => 'DEDI P',        'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2020-09-19'],
            ['nama' => 'ZAKIR',         'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2021-09-02'],
            ['nama' => 'ROJIKIN',       'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2019-07-07'],
            ['nama' => 'SUNAR',         'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2024-05-21'],
            ['nama' => 'YADI',          'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2023-04-01'],
            ['nama' => 'DODIK',         'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2019-11-02'],
            ['nama' => 'MISRAN',        'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2023-07-15'],
            ['nama' => 'JAYA',          'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2025-05-08'],
            ['nama' => 'YANSA',         'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2023-08-12'],
            ['nama' => 'AGUS KARIM',    'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2022-05-23'],
            ['nama' => 'JUPRI',         'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2022-11-17'],
            ['nama' => 'SOBRI',         'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2020-01-12'],
            ['nama' => 'SELAMET',       'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2022-06-02'],
            ['nama' => 'WANDA',         'jenis_kelamin' => 'Perempuan',  'tgl_masuk' => '2025-01-15'],
            ['nama' => 'ARI',           'jenis_kelamin' => 'Laki-laki',  'tgl_masuk' => '2025-03-02'],
        ];

        foreach ($dataKaryawan as $k) {
            \App\Models\Karyawan::create([
                'nama'           => $k['nama'],
                'jenis_kelamin'  => $k['jenis_kelamin'],
                'tgl_masuk'      => $k['tgl_masuk'],
                'tgl_lahir'      => null,
                'status'         => 'aktif',
                'no_telepon'     => null,
                'alamat'         => null,
            ]);
        }

        // ── KRITERIA & SUB-KRITERIA ───────────────────────────────────
        $kriteriaData = [
            ['nama' => 'Kehadiran',      'jenis' => 'benefit', 'bobot' => 30, 'has_rentang' => true,  'satuan_rentang' => 'hari'],
            ['nama' => 'Masa Kerja',     'jenis' => 'benefit', 'bobot' => 15, 'has_rentang' => true,  'satuan_rentang' => 'tahun'],
            ['nama' => 'Kedisiplinan',   'jenis' => 'cost',    'bobot' => 20, 'has_rentang' => true,  'satuan_rentang' => 'kali'],
            ['nama' => 'Tanggung Jawab', 'jenis' => 'benefit', 'bobot' => 20, 'has_rentang' => false, 'satuan_rentang' => null],
            ['nama' => 'Komunikasi',     'jenis' => 'benefit', 'bobot' => 15, 'has_rentang' => false, 'satuan_rentang' => null],
        ];

        $subKriteriaData = [
            'Kehadiran' => [
                ['skor' => 5, 'nama' => '≥ 26 hari',   'nilai_min' => 26, 'nilai_max' => 99],
                ['skor' => 4, 'nama' => '23 – 25 hari', 'nilai_min' => 23, 'nilai_max' => 25],
                ['skor' => 3, 'nama' => '20 – 22 hari', 'nilai_min' => 20, 'nilai_max' => 22],
                ['skor' => 2, 'nama' => '17 – 19 hari', 'nilai_min' => 17, 'nilai_max' => 19],
                ['skor' => 1, 'nama' => '< 17 hari',    'nilai_min' => 0,  'nilai_max' => 16],
            ],
            'Masa Kerja' => [
                ['skor' => 5, 'nama' => '> 10 tahun',     'nilai_min' => 10.01, 'nilai_max' => 99],
                ['skor' => 4, 'nama' => '> 5 – 10 tahun', 'nilai_min' => 5.01,  'nilai_max' => 10],
                ['skor' => 3, 'nama' => '> 3 – 5 tahun',  'nilai_min' => 3.01,  'nilai_max' => 5],
                ['skor' => 2, 'nama' => '1 – 3 tahun',    'nilai_min' => 1,     'nilai_max' => 3],
                ['skor' => 1, 'nama' => '< 1 tahun',      'nilai_min' => 0,     'nilai_max' => 0],
            ],
            'Kedisiplinan' => [
                ['skor' => 1, 'nama' => 'Tidak pernah terlambat', 'nilai_min' => 0, 'nilai_max' => 0],
                ['skor' => 2, 'nama' => '1 kali terlambat',        'nilai_min' => 1, 'nilai_max' => 1],
                ['skor' => 3, 'nama' => '2 kali terlambat',        'nilai_min' => 2, 'nilai_max' => 2],
                ['skor' => 4, 'nama' => '3 kali terlambat',        'nilai_min' => 3, 'nilai_max' => 3],
                ['skor' => 5, 'nama' => '> 3 kali terlambat',      'nilai_min' => 4, 'nilai_max' => 99],
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
            $kriteria = Kriteria::create($kd);

            foreach ($subKriteriaData[$kd['nama']] ?? [] as $sk) {
                SubKriteria::create([
                    'kriteria_id' => $kriteria->id,
                    'nama'        => $sk['nama'],
                    'skor'        => $sk['skor'],
                    'nilai_min'   => $sk['nilai_min'] ?? null,
                    'nilai_max'   => $sk['nilai_max'] ?? null,
                    'keterangan'  => $sk['nama'],
                ]);
            }
        }

        // ── PERIODE ───────────────────────────────────────────────────
        // Periode TIDAK dibuat di seeder.
        // → Direktur membuat periode sendiri lewat menu Periode,
        //   snapshot kriteria & sub-kriteria otomatis tersalin saat itu.
    }
}