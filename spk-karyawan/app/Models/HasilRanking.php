<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasilRanking extends Model
{
    protected $table = 'hasil_ranking';

    protected $fillable = [
        'periode_id', 'karyawan_id',
        'nilai_preferensi', 'ranking',
    ];

    protected $casts = [
        'nilai_preferensi' => 'float',
        'ranking'          => 'integer',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'periode_id', 'periode_id')
            ->where('karyawan_id', $this->karyawan_id);
    }
}