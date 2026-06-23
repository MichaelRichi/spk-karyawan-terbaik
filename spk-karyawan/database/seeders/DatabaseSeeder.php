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
            ['nama' => 'PAIDI',         'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2016-03-28', 'tgl_lahir' => '1976-10-31', 'no_telepon' => '085368189216', 'alamat' => 'Jl. Slamet Riady, Lr. Tapakning No.258B'],
            ['nama' => 'HERU',          'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2019-07-17', 'tgl_lahir' => '2003-09-14', 'no_telepon' => '08117826026', 'alamat' => 'Jl. Letnan Hadin No.03'],
            ['nama' => 'RIZKI',         'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2023-02-03', 'tgl_lahir' => '1966-07-03', 'no_telepon' => '083178935246', 'alamat' => 'Jalan Bungaran 1 NG 30 rt/RW 001/001 kel 8 ulu kec seberang ulu 1'],
            ['nama' => 'DAWI',          'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2025-07-15', 'tgl_lahir' => '1972-02-22', 'no_telepon' => '089507071986', 'alamat' => 'KOMP. VILLA SUKAMAJU BLOK B 21'],
            ['nama' => 'SELAMET SUPIR', 'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2024-10-24', 'tgl_lahir' => '2001-05-11', 'no_telepon' => '0895621685648', 'alamat' => 'Jl. Abikusno cs Lr. Patria'],
            ['nama' => 'BOBO',          'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2025-05-12', 'tgl_lahir' => '2002-06-18', 'no_telepon' => '08974134304', 'alamat' => 'Perum Opi Jakabaring.'],
            ['nama' => 'DARSO',         'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2024-09-26', 'tgl_lahir' => '1985-08-25', 'no_telepon' => '089677074224', 'alamat' => 'Jl. Gersik Lr. Pakis no. 1528 RT 031 RW 006 Kel/desa sembilan ilir
Kecamatan ilir timur tiga'],
            ['nama' => 'JUMADI',        'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2018-06-07', 'tgl_lahir' => '2005-09-19', 'no_telepon' => '083189163493', 'alamat' => 'Jl. DI.Panjaitan Lr. Lama Plaju'],
            ['nama' => 'RIKO B',        'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2018-07-27', 'tgl_lahir' => '1993-06-27', 'no_telepon' => '083185258082', 'alamat' => 'Jalan Paqih Usman 1 Ulu Lorong Lebak'],
            ['nama' => 'RIA',           'jenis_kelamin' => 'Perempuan', 'tgl_masuk' => '2021-02-28', 'tgl_lahir' => '2004-11-16', 'no_telepon' => '0895615430831', 'alamat' => 'Jl. Jaya Vll No 15'],
            ['nama' => 'NADYA',         'jenis_kelamin' => 'Perempuan', 'tgl_masuk' => '2019-07-01', 'tgl_lahir' => '2002-04-09', 'no_telepon' => '087823237100', 'alamat' => 'LRG. HIBAH 1 NO. 13 ASRAMA DOSEN RT/RW 026/009 KEL. BUKIT LAMA KEC. ILIR BARAT I'],
            ['nama' => 'NINI',          'jenis_kelamin' => 'Perempuan', 'tgl_masuk' => '2019-10-31', 'tgl_lahir' => '1970-11-30', 'no_telepon' => '089677797498', 'alamat' => 'JL. PERIKANAN'],
            ['nama' => 'YULI',          'jenis_kelamin' => 'Perempuan', 'tgl_masuk' => '2016-05-21', 'tgl_lahir' => '1998-03-06', 'no_telepon' => '085383000266', 'alamat' => 'Jln. Kkn unsri No. 2154'],
            ['nama' => 'NATHASYIA',     'jenis_kelamin' => 'Perempuan', 'tgl_masuk' => '2018-12-11', 'tgl_lahir' => '2002-11-30', 'no_telepon' => '089513004997', 'alamat' => 'JL KEBON MANGGIS NO. 03'],
            ['nama' => 'RARA',          'jenis_kelamin' => 'Perempuan', 'tgl_masuk' => '2017-04-08', 'tgl_lahir' => '1999-01-08', 'no_telepon' => '08994474104', 'alamat' => 'Jl perpetak no 12b bukit sangkal kalidoni'],
            ['nama' => 'LOKI',          'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2018-08-03', 'tgl_lahir' => '1987-11-19', 'no_telepon' => '083178012064', 'alamat' => 'Jln. Puding. No. 4127'],
            ['nama' => 'DEDI P',        'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2020-09-19', 'tgl_lahir' => '1997-02-02', 'no_telepon' => '081373338012', 'alamat' => 'Jl Ki Gede ing suro lr serengam 1 no 429'],
            ['nama' => 'ZAKIR',         'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2021-09-02', 'tgl_lahir' => '1991-03-07', 'no_telepon' => '089519487464', 'alamat' => 'Jl.di.panjaitan lr.lama Plaju RT.009/RW.003'],
            ['nama' => 'ROJIKIN',       'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2019-07-07', 'tgl_lahir' => '1995-08-23', 'no_telepon' => '081389550817', 'alamat' => 'Jl.surya sakti RT 33 RW 11 Kec Sukarami Kel Sukarami no.1997 Palembang'],
            ['nama' => 'SUNAR',         'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2024-05-21', 'tgl_lahir' => '1997-12-10', 'no_telepon' => '08974134304', 'alamat' => 'Jalan paqih usman 1 ulu lorong Lebak kec seberang ulu 1 palembang'],
            ['nama' => 'YADI',          'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2023-04-01', 'tgl_lahir' => '2002-09-18', 'no_telepon' => '083178012064', 'alamat' => 'Jl. Pangeran Sidoing Lautan Lorong Kedukan Bukit 1 No.125'],
            ['nama' => 'DODIK',         'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2019-11-02', 'tgl_lahir' => '1992-12-21', 'no_telepon' => '089677074224', 'alamat' => 'Jl. Ramakasih 5 no. 874'],
            ['nama' => 'MISRAN',        'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2023-07-15', 'tgl_lahir' => '2004-06-04', 'no_telepon' => '089663502344', 'alamat' => 'Jln Opi 6 lrg tembesu 2 blok O 27'],
            ['nama' => 'JAYA',          'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2025-05-08', 'tgl_lahir' => '1970-02-25', 'no_telepon' => '083189163493', 'alamat' => 'Jln ps ing kenayan rt 08 rw 03 kel karang anyar kec gandus palembang'],
            ['nama' => 'YANSA',         'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2023-08-12', 'tgl_lahir' => '1985-01-15', 'no_telepon' => '085368189216', 'alamat' => 'Jln. Puding. No. 4125'],
            ['nama' => 'AGUS KARIM',    'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2022-05-23', 'tgl_lahir' => '1972-09-01', 'no_telepon' => '085366722032', 'alamat' => 'Jl. Abikusno cs Lr. Patria'],
            ['nama' => 'JUPRI',         'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2022-11-17', 'tgl_lahir' => '2004-04-17', 'no_telepon' => '08127112151', 'alamat' => 'JL.Gersik LR.Pakis No.1528'],
            ['nama' => 'SOBRI',         'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2020-01-12', 'tgl_lahir' => '2003-07-28', 'no_telepon' => '083185258082', 'alamat' => 'Jl. Kapten Marzuki No. 520'],
            ['nama' => 'JIMI TUKIMIN',  'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2022-06-02', 'tgl_lahir' => '2004-01-20', 'no_telepon' => '083178935246', 'alamat' => 'JALAN RAMAKASIH VI'],
            ['nama' => 'WANDA',         'jenis_kelamin' => 'Perempuan', 'tgl_masuk' => '2025-01-15', 'tgl_lahir' => '1995-09-18', 'no_telepon' => '082269766976', 'alamat' => 'Jl. Ramakasih 3 no. 655'],
            ['nama' => 'ARI',           'jenis_kelamin' => 'Laki-laki', 'tgl_masuk' => '2025-03-02', 'tgl_lahir' => '2000-08-18', 'no_telepon' => '0895621685648', 'alamat' => 'Jl. Surya Sakti No.1997'],
        ];

        foreach ($dataKaryawan as $k) {
            \App\Models\Karyawan::create([
                'nama'           => $k['nama'],
                'jenis_kelamin'  => $k['jenis_kelamin'],
                'tgl_masuk'      => $k['tgl_masuk'],
                'tgl_lahir'      => $k['tgl_lahir'] ?? null,
                'status'         => 'aktif',
                'no_telepon'     => $k['no_telepon'] ?? null,
                'alamat'         => $k['alamat'] ?? null,
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