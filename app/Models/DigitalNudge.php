<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalNudge extends Model
{
    protected $fillable = [
        'user_id',
        'tipe_nudge',
        'diabaikan',
    ];

    protected function casts(): array
    {
        return [
            'diabaikan' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
