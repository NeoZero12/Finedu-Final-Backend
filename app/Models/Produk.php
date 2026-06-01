<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = [
        'nama_produk',
        'harga',
        'kategori',
        'gambar_url',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiSimulasi::class);
    }
}
