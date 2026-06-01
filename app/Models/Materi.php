<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'modul_id',
        'judul_materi',
        'konten',
    ];

    public function modul()
    {
        return $this->belongsTo(ModulPembelajaran::class, 'modul_id');
    }
}
