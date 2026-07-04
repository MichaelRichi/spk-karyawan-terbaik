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

    /** Daftar tipe kepegawaian yang didukung */
    public static function tipeList(): array
    {
        return ['tetap', 'tidak_tetap'];
    }

    public static function tipeLabel(string $tipe): string
    {
        return $tipe === 'tetap' ? 'Karyawan Tetap' : 'Karyawan Tidak Tetap';
    }

    /** Snapshot kriteria untuk satu tipe kepegawaian */
    public function periodeKriteriaTipe(string $tipe)
    {
        return $this->periodeKriteria()->where('tipe', $tipe);
    }

    /**
     * Cari periode_kriteria (dalam satu tipe) berdasarkan satuan rentang,
     * lalu fallback ke nama. Dipakai untuk auto-isi nilai dari data absensi.
     */
    public function cariKriteria(?string $satuan, string $namaLike, string $tipe = 'tetap')
    {
        $kriteria = null;

        if ($satuan) {
            $kriteria = $this->periodeKriteria()->with('periodeSubKriteria')
                ->where('tipe', $tipe)
                ->whereHas('kriteria', fn($k) => $k->where('satuan_rentang', $satuan))
                ->first();
        }

        if (!$kriteria) {
            $kriteria = $this->periodeKriteria()->with('periodeSubKriteria')
                ->where('tipe', $tipe)
                ->whereRaw('LOWER(nama_kriteria) LIKE ?', ['%' . strtolower($namaLike) . '%'])
                ->first();
        }

        return $kriteria;
    }

    /** Kriteria Kehadiran (satuan 'hari') untuk tipe tertentu */
    public function kriteriaKehadiran(string $tipe = 'tetap')
    {
        return $this->cariKriteria('hari', 'kehadiran', $tipe);
    }

    /** Kriteria Kedisiplinan (satuan 'kali') untuk tipe tertentu */
    public function kriteriaKedisiplinan(string $tipe = 'tetap')
    {
        return $this->cariKriteria('kali', 'disiplin', $tipe);
    }

    /** Validasi: total bobot kriteria satu tipe harus = 100% */
    public function isBobotValid(string $tipe): bool
    {
        $total = $this->periodeKriteria()->where('tipe', $tipe)->sum('bobot');
        return abs($total - 100) < 0.01;
    }

    /** Cek apakah semua karyawan aktif tipe tertentu sudah dinilai lengkap */
    public function isInputLengkap(string $tipe): bool
    {
        $karyawanIds     = Karyawan::aktif()->tipe($tipe)->pluck('id');
        $jumlahKaryawan  = $karyawanIds->count();
        $jumlahKriteria  = $this->periodeKriteria()->where('tipe', $tipe)->count();
        if ($jumlahKaryawan === 0 || $jumlahKriteria === 0) {
            return false;
        }
        $jumlahPenilaian = $this->penilaian()->whereIn('karyawan_id', $karyawanIds)->count();
        return $jumlahPenilaian >= ($jumlahKaryawan * $jumlahKriteria);
    }

    /** Apakah ranking tipe tertentu sudah dihitung */
    public function sudahDihitung(string $tipe): bool
    {
        return $this->hasilRanking()->where('tipe', $tipe)->exists();
    }
}