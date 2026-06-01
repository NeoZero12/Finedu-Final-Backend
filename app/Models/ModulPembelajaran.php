<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulPembelajaran extends Model
{
    protected $fillable = [
        'judul_modul',
        'deskripsi',
    ];

    public function materis()
    {
        return $this->hasMany(Materi::class, 'modul_id');
    }

    public function progresses()
    {
        return $this->hasMany(ModulProgress::class, 'modul_pembelajaran_id');
    }

    public function kuesioners()
    {
        return $this->hasMany(Kuesioner::class, 'modul_pembelajaran_id');
    }
}
