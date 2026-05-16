<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodeSubKriteria extends Model
{
    protected $table = 'periode_sub_kriteria';

    protected $fillable = [
        'periode_id', 'periode_kriteria_id', 'sub_kriteria_id',
        'label', 'skor', 'keterangan',
    ];

    protected $casts = [
        'skor' => 'integer',
    ];

    public function periodeKriteria(): BelongsTo
    {
        return $this->belongsTo(PeriodeKriteria::class);
    }

    public function subKriteria(): BelongsTo
    {
        return $this->belongsTo(SubKriteria::class);
    }
}