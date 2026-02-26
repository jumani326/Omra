<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilgrimDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'pilgrim_id',
        'type',
        'file_path',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    // Relations
    public function pilgrim()
    {
        return $this->belongsTo(Pilgrim::class);
    }

    // Scopes
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}
