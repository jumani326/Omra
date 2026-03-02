<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public const METHOD_LABELS = [
        'cash' => 'Espèces',
        'cash_espece' => 'Cash espèce',
        'transfer' => 'Virement',
        'tpe' => 'TPE',
        'mobile_money' => 'Mobile Money',
    ];

    protected $fillable = [
        'pilgrim_id',
        'amount',
        'method',
        'status',
        'ref_no',
        'processed_by',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    // Relations
    public function pilgrim()
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHOD_LABELS[$this->method] ?? ucfirst(str_replace('_', ' ', $this->method));
    }
}
