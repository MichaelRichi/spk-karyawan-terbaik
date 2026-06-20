<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Periode extends Model
{
    protected $table = 'periode';

    protected $fillable = [
        'nama', 'bulan', 'tahun', 'status', 'keterangan',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
    ];

    public function periodeKriteria(): HasMany
    {
        return $this->hasMany(PeriodeKriteria::class)->orderBy('id');
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function hasilRanking(): HasMany
    {
        return $this->hasMany(HasilRanking::class)->orderBy('ranking');
    }

    /** Nama bulan dalam Bahasa Indonesia */
    public function getNamaBulanLengkapAttribute(): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',      6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',  9 => 'September',
            10 => 'Oktober',11 => 'November',12 => 'Desember',
        ];
        return ($bulan[$this->bulan] ?? '') . ' ' . $this->tahun;
    }

    /** Periode sudah dikunci, tidak bisa diedit */
    public function isLocked(): bool
    {
        return $this->status === 'selesai';
    }

    /**
     * Cari periode_kriteria berdasarkan satuan rentang, lalu fallback ke nama.
     * Dipakai untuk auto-isi nilai dari data absensi.
     */
    public function cariKriteria(?string $satuan, string $namaLike)
    {
        $kriteria = null;

        if ($satuan) {
            $kriteria = $this->periodeKriteria()->with('periodeSubKriteria')
                ->whereHas('kriteria', fn($k) => $k->where('satuan_rentang', $satuan))
                ->first();
        }

        if (!$kriteria) {
            $kriteria = $this->periodeKriteria()->with('periodeSubKriteria')
                ->whereRaw('LOWER(nama_kriteria) LIKE ?', ['%' . strtolower($namaLike) . '%'])
                ->first();
        }

        return $kriteria;
    }

    /** Kriteria Kehadiran (satuan 'hari') */
    public function kriteriaKehadiran()
    {
        return $this->cariKriteria('hari', 'kehadiran');
    }

    /** Kriteria Kedisiplinan (satuan 'kali') */
    public function kriteriaKedisiplinan()
    {
        return $this->cariKriteria('kali', 'disiplin');
    }

    /** Validasi: total bobot kriteria harus = 100% */
    public function isBobotValid(): bool
    {
        $total = $this->periodeKriteria()->sum('bobot');
        return abs($total - 100) < 0.01;
    }

    /** Cek apakah semua karyawan aktif sudah dinilai */
    public function isInputLengkap(): bool
    {
        $jumlahKaryawan  = Karyawan::aktif()->count();
        $jumlahKriteria  = $this->periodeKriteria()->count();
        $jumlahPenilaian = $this->penilaian()->count();
        return $jumlahPenilaian >= ($jumlahKaryawan * $jumlahKriteria);
    }
}