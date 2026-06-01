<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiSimulasi extends Model
{
    protected $fillable = [
        'simulasi_id',
        'produk_id',
        'nama_item',
        'kategori_label',
        'catatan',
        'arah_transaksi',
        'nominal',
        'pembelian_impulsif',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'pembelian_impulsif' => 'boolean',
        ];
    }

    public function simulasi()
    {
        return $this->belongsTo(Simulasi::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
