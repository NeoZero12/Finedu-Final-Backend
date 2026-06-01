<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilSkor extends Model
{
    protected $fillable = [
        'user_id',
        'skor_literasi_akhir',
        'total_overspending',
    ];

    protected function casts(): array
    {
        return [
            'total_overspending' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
