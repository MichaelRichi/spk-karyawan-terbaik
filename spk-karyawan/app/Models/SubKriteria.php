<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubKriteria extends Model
{
    protected $table = 'sub_kriteria';

    protected $fillable = [
        'kriteria_id', 'nama', 'skor', 'keterangan', 'nilai_min', 'nilai_max',
    ];

    protected $casts = [
        'skor' => 'integer',
    ];

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function periodeSubKriteria(): HasMany
    {
        return $this->hasMany(PeriodeSubKriteria::class);
    }
}