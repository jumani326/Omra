<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'pilgrim_id',
        'messages',
        'lang',
        'escalated',
        'escalated_to',
    ];

    protected function casts(): array
    {
        return [
            'messages' => 'array',
            'escalated' => 'boolean',
        ];
    }

    // Relations
    public function pilgrim()
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function escalatedTo()
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }
}
