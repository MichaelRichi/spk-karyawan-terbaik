<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = ['karyawan_id', 'tanggal', 'status', 'terlambat', 'keterangan'];

    protected $casts = ['tanggal' => 'date', 'terlambat' => 'boolean'];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Total hari hadir karyawan di bulan & tahun tertentu.
     */
    public static function totalHadir(int $karyawanId, int $bulan, int $tahun): int
    {
        return static::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'hadir')
            ->count();
    }

    /**
     * Total hari terlambat karyawan di bulan & tahun tertentu.
     * (untuk kriteria Kedisiplinan)
     */
    public static function totalTerlambat(int $karyawanId, int $bulan, int $tahun): int
    {
        return static::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('terlambat', true)
            ->count();
    }

    /**
     * Hitung skor dari total hari hadir.
     *
     * Dibaca dari sub_kriteria master (nilai_min & nilai_max) yang
     * terhubung ke periode_sub_kriteria via sub_kriteria_id.
     * Tidak ada nilai hardcode — semua dari database.
     *
     * Fallback hardcode hanya dipakai jika direktur belum mengisi
     * nilai_min/nilai_max di sub kriteria.
     */
    public static function hitungSkor(int $totalHadir, int $periodeKriteriaId): int
    {
        // Ambil periode_sub_kriteria beserta sub_kriteria master-nya
        // diurutkan dari skor tertinggi agar rentang terbesar dicek dulu
        $psks = PeriodeSubKriteria::where('periode_kriteria_id', $periodeKriteriaId)
            ->with('subKriteria')   // join ke sub_kriteria master untuk ambil nilai_min/nilai_max
            ->get()
            ->sortByDesc('skor');

        foreach ($psks as $psk) {
            $sk = $psk->subKriteria; // data master sub kriteria (punya nilai_min & nilai_max)

            if (!$sk || $sk->nilai_min === null || $sk->nilai_max === null) {
                continue; // sub kriteria ini belum diisi rentangnya, lewati
            }

            $min = (float) $sk->nilai_min;
            $max = (float) $sk->nilai_max;

            // nilai_max >= 99 dianggap tidak terbatas ke atas (misal: >= 26 hari)
            if ($max >= 99 && $totalHadir >= $min) return $psk->skor;
            if ($totalHadir >= $min && $totalHadir <= $max) return $psk->skor;
        }

        // Fallback: hanya dipakai jika SEMUA sub kriteria belum punya nilai_min/nilai_max
        // Artinya direktur belum mengisi rentang di halaman Sub Kriteria
        if ($totalHadir >= 26)     return 5;
        elseif ($totalHadir >= 23) return 4;
        elseif ($totalHadir >= 20) return 3;
        elseif ($totalHadir >= 17) return 2;
        else                        return 1;
    }
}