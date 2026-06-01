<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kuesioner extends Model
{
    protected $fillable = [
        'user_id',
        'modul_pembelajaran_id',
        'nomor_soal',
        'pertanyaan',
        'opsi_jawaban',
        'jawaban_benar',
        'jawaban_skala',
        'benar',
        'attempt_count',
    ];

    protected function casts(): array
    {
        return [
            'opsi_jawaban' => 'array',
            'benar' => 'boolean',
            'attempt_count' => 'integer',
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
