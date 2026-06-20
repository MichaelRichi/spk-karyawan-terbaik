<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $periode_id
 * @property int $karyawan_id
 * @property int $periode_kriteria_id
 * @property int|null $periode_sub_kriteria_id
 * @property int $nilai
 * @property float|null $nilai_normalisasi
 * @property float|null $nilai_terbobot
 * @property string|null $catatan
 */
class Penilaian extends Model
{
    protected $table = 'penilaian';

    protected $fillable = [
        'periode_id', 'karyawan_id', 'periode_kriteria_id',
        'periode_sub_kriteria_id', 'nilai',
        'nilai_normalisasi', 'nilai_terbobot',
        'catatan',
    ];

    protected $casts = [
        'nilai'             => 'integer',
        'nilai_normalisasi' => 'float',
        'nilai_terbobot'    => 'float',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function periodeKriteria(): BelongsTo
    {
        return $this->belongsTo(PeriodeKriteria::class);
    }

    public function periodeSubKriteria(): BelongsTo
    {
        return $this->belongsTo(PeriodeSubKriteria::class);
    }
}