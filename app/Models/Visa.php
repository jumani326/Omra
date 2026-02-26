<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visa extends Model
{
    use HasFactory;

    protected $fillable = [
        'pilgrim_id',
        'status',
        'submitted_at',
        'decision_at',
        'expiry_date',
        'refusal_reason',
        'reference_no',
        'documents',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'decision_at' => 'datetime',
            'expiry_date' => 'date',
            'documents' => 'array',
        ];
    }

    // Relations
    public function pilgrim()
    {
        return $this->belongsTo(Pilgrim::class);
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                     ->where('expiry_date', '>=', now());
    }
}
