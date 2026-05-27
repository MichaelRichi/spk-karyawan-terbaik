<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    // Paksa nama tabel — Laravel default akan pakai 'karyawans'
    protected $table = 'karyawan';

    protected $fillable = [
        'nama', 'tgl_lahir', 'jenis_kelamin',
        'tgl_masuk', 'status',
        'no_telepon', 'alamat',
    ];

    protected $casts = [
        'tgl_masuk' => 'date', 'tgl_lahir' => 'date',
    ];

    // Scope hanya karyawan aktif — dipakai di penilaian
    public function scopeAktif(Builder $query)
    {
        return $query->where('status', 'aktif');
    }

    // Relasi
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function hasilRanking()
    {
        return $this->hasMany(HasilRanking::class);
    }

    // Helper

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}