<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'pilgrim_id',
        'user_id',
        'action',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    // Relations
    public function pilgrim()
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
