<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    protected $table = 'kriteria';

    protected $fillable = [
        'nama', 'jenis', 'bobot', 'has_rentang', 'satuan_rentang',
    ];

    protected $casts = [
        'bobot' => 'float',
        'has_rentang' => 'boolean',
    ];

    public function subKriteria(): HasMany
    {
        return $this->hasMany(SubKriteria::class)->orderBy('skor');
    }

    public function periodeKriteria(): HasMany
    {
        return $this->hasMany(PeriodeKriteria::class);
    }
}