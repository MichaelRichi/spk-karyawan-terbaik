<?php

namespace Database\Seeders;

use App\Models\Karyawan;
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

        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Karyawan (status aktif/tidak_aktif) ───────
        $karyawan = [
            ['nama' => 'Paidi',  'jabatan' => 'Staff Lapangan', 'jenis_kelamin' => 'laki-laki',  'tanggal_masuk' => '2010-01-15', 'status' => 'aktif'],
            ['nama' => 'Heru',   'jabatan' => 'Staff Lapangan', 'jenis_kelamin' => 'laki-laki',  'tanggal_masuk' => '2012-03-10', 'status' => 'aktif'],
            ['nama' => 'Rizki',  'jabatan' => 'Teknisi',        'jenis_kelamin' => 'laki-laki',  'tanggal_masuk' => '2019-07-01', 'status' => 'aktif'],
            ['nama' => 'Dawi',   'jabatan' => 'Teknisi',        'jenis_kelamin' => 'laki-laki',  'tanggal_masuk' => '2022-02-01', 'status' => 'aktif'],
            ['nama' => 'Darso',  'jabatan' => 'Staff Lapangan', 'jenis_kelamin' => 'laki-laki',  'tanggal_masuk' => '2023-06-01', 'status' => 'tidak_aktif'],
        ];

        foreach ($karyawan as $k) {
            Karyawan::create($k);
        }

        // ── Kriteria ──────────────────────────────────
        $kriteriaData = [
            ['nama' => 'Kehadiran',      'jenis' => 'benefit', 'bobot_default' => 30],
            ['nama' => 'Masa Kerja',     'jenis' => 'benefit', 'bobot_default' => 15],
            ['nama' => 'Kedisiplinan',   'jenis' => 'cost',    'bobot_default' => 20],
            ['nama' => 'Tanggung Jawab', 'jenis' => 'benefit', 'bobot_default' => 20],
            ['nama' => 'Komunikasi',     'jenis' => 'benefit', 'bobot_default' => 15],
        ];

        $subKriteriaData = [
            'Kehadiran' => [
                ['skor' => 5, 'nama' => '≥ 26 hari kerja',  'keterangan' => 'Hadir 26 hari atau lebih dalam sebulan'],
                ['skor' => 4, 'nama' => '23–25 hari kerja', 'keterangan' => 'Hadir antara 23 sampai 25 hari dalam sebulan'],
                ['skor' => 3, 'nama' => '20–22 hari kerja', 'keterangan' => 'Hadir antara 20 sampai 22 hari dalam sebulan'],
                ['skor' => 2, 'nama' => '17–19 hari kerja', 'keterangan' => 'Hadir antara 17 sampai 19 hari dalam sebulan'],
                ['skor' => 1, 'nama' => '< 17 hari kerja',  'keterangan' => 'Hadir kurang dari 17 hari dalam sebulan'],
            ],
            'Masa Kerja' => [
                ['skor' => 5, 'nama' => '> 10 tahun',  'keterangan' => 'Telah bekerja lebih dari 10 tahun'],
                ['skor' => 4, 'nama' => '5–10 tahun',  'keterangan' => 'Telah bekerja antara 5 sampai 10 tahun'],
                ['skor' => 3, 'nama' => '3–5 tahun',   'keterangan' => 'Telah bekerja antara 3 sampai 5 tahun'],
                ['skor' => 2, 'nama' => '1–3 tahun',   'keterangan' => 'Telah bekerja antara 1 sampai 3 tahun'],
                ['skor' => 1, 'nama' => '< 1 tahun',   'keterangan' => 'Telah bekerja kurang dari 1 tahun'],
            ],
            'Kedisiplinan' => [
                ['skor' => 1, 'nama' => 'Tidak pernah terlambat', 'keterangan' => 'Tidak ada catatan keterlambatan dalam sebulan'],
                ['skor' => 2, 'nama' => '1 kali terlambat',       'keterangan' => 'Tercatat terlambat 1 kali dalam sebulan'],
                ['skor' => 3, 'nama' => '2 kali terlambat',       'keterangan' => 'Tercatat terlambat 2 kali dalam sebulan'],
                ['skor' => 4, 'nama' => '3 kali terlambat',       'keterangan' => 'Tercatat terlambat 3 kali dalam sebulan'],
                ['skor' => 5, 'nama' => '> 3 kali terlambat',     'keterangan' => 'Tercatat terlambat lebih dari 3 kali dalam sebulan'],
            ],
            'Tanggung Jawab' => [
                ['skor' => 5, 'nama' => 'Sangat bagus', 'keterangan' => 'Selalu menyelesaikan tugas tepat waktu dan melampaui ekspektasi'],
                ['skor' => 4, 'nama' => 'Bagus',        'keterangan' => 'Menyelesaikan tugas tepat waktu sesuai ekspektasi'],
                ['skor' => 3, 'nama' => 'Cukup',        'keterangan' => 'Menyelesaikan sebagian besar tugas, kadang perlu diingatkan'],
                ['skor' => 2, 'nama' => 'Kurang',       'keterangan' => 'Sering tidak menyelesaikan tugas tepat waktu'],
                ['skor' => 1, 'nama' => 'Sangat kurang','keterangan' => 'Jarang menyelesaikan tugas dan perlu pengawasan ketat'],
            ],
            'Komunikasi' => [
                ['skor' => 5, 'nama' => 'Sangat bagus', 'keterangan' => 'Komunikasi sangat baik, aktif dan jelas dalam menyampaikan informasi'],
                ['skor' => 4, 'nama' => 'Bagus',        'keterangan' => 'Komunikasi baik dan mampu bekerja sama dengan tim'],
                ['skor' => 3, 'nama' => 'Cukup',        'keterangan' => 'Komunikasi cukup, kadang perlu didorong untuk menyampaikan informasi'],
                ['skor' => 2, 'nama' => 'Kurang',       'keterangan' => 'Komunikasi kurang, sering menimbulkan miskomunikasi'],
                ['skor' => 1, 'nama' => 'Sangat kurang','keterangan' => 'Komunikasi sangat buruk, sulit berkoordinasi dengan tim'],
            ],
        ];

        foreach ($kriteriaData as $kd) {
            $k = Kriteria::create($kd);
            foreach ($subKriteriaData[$kd['nama']] as $sk) {
                SubKriteria::create([
                    'kriteria_id' => $k->id,
                    'nama'        => $sk['nama'],
                    'skor'        => $sk['skor'],
                    'keterangan'  => $sk['keterangan'],
                ]);
            }
        }
    }
}