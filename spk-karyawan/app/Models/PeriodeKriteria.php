<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeKriteria extends Model
{
    protected $table = 'periode_kriteria';

    protected $fillable = [
        'periode_id', 'kriteria_id', 'tipe',
        'nama_kriteria', 'jenis', 'bobot',
    ];

    protected $casts = [
        'bobot' => 'float',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function periodeSubKriteria(): HasMany
    {
        return $this->hasMany(PeriodeSubKriteria::class)->orderBy('skor');
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    /** Bobot dalam desimal (0–1) untuk perhitungan SAW */
    public function getBobotDesimalAttribute(): float
    {
        return $this->bobot / 100;
    }
}