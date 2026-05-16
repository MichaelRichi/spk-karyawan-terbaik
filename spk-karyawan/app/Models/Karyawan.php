<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $nama
 * @property string $jabatan
 * @property string $jenis_kelamin
 * @property \Carbon\Carbon $tanggal_masuk
 * @property string $status
 * @property int $masa_kerja
 */
class Karyawan extends Model
{
    protected $table = 'karyawan';

    protected $fillable = [
        'nama', 'jabatan', 'jenis_kelamin',
        'tanggal_masuk', 'status',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function hasilRanking(): HasMany
    {
        return $this->hasMany(HasilRanking::class);
    }

    /** Hitung masa kerja dalam tahun (untuk auto-skor C2) */
    public function getMasaKerjaAttribute(): int
    {
        return (int) Carbon::parse($this->tanggal_masuk)->diffInYears(now());
    }
}