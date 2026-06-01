<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulasi extends Model
{
    protected $fillable = [
        'user_id',
        'anggaran_awal',
        'anggaran_sisa',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'anggaran_awal' => 'decimal:2',
            'anggaran_sisa' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiSimulasi::class);
    }
}
