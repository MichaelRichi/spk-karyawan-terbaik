<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $username
 * @property string $role
 * @property int|null $karyawan_id
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'password', 'role', 'karyawan_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    // Laravel pakai 'username' bukan 'email' untuk login
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isDirektur(): bool { return $this->role === 'direktur'; }
    public function isKaryawan(): bool { return $this->role === 'karyawan'; }
    public function canManage(): bool  { return in_array($this->role, ['admin', 'direktur']); }
}