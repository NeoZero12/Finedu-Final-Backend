<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// ✅ Tambahan Sanctum
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // ✅ Field yang boleh diisi
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'email_verified_at',
    ];

    // ✅ Field yang disembunyikan
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'nama',
    ];

    // ✅ Casting
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ✅ Relasi ke Profil
    public function profil()
    {
        return $this->hasOne(Profil::class);
    }

    public function simulasis()
    {
        return $this->hasMany(Simulasi::class);
    }

    public function hasilSkors()
    {
        return $this->hasMany(HasilSkor::class);
    }

    public function modulProgresses()
    {
        return $this->hasMany(ModulProgress::class);
    }

    public function getNamaAttribute(): string
    {
        return $this->name;
    }
}
