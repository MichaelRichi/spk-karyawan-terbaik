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
        'tgl_masuk', 'status', 'tipe',
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

    // Scope berdasarkan jenis kepegawaian (tetap / tidak_tetap)
    public function scopeTipe(Builder $query, string $tipe)
    {
        return $query->where('tipe', $tipe);
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

    /** Label jenis kepegawaian untuk tampilan */
    public function getTipeLabelAttribute(): string
    {
        return $this->tipe === 'tetap' ? 'Tetap' : 'Tidak Tetap';
    }
}