<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulProgress extends Model
{
    protected $table = 'modul_progresses';

    protected $fillable = [
        'user_id',
        'modul_pembelajaran_id',
        'selesai',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'selesai' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function modul()
    {
        return $this->belongsTo(ModulPembelajaran::class, 'modul_pembelajaran_id');
    }
}
