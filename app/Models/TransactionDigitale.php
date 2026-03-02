<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDigitale extends Model
{
    use HasFactory;

    protected $table = 'transaction_digitales';

    public const STATUTS = ['en_attente', 'valide', 'refuse'];

    protected $fillable = [
        'compte_marchand_id',
        'pilgrim_id',
        'montant',
        'client_nom',
        'statut',
        'reference',
        'notes',
        'pdf_path',
        'validation_email_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'validation_email_sent_at' => 'datetime',
        ];
    }

    public function compteMarchand()
    {
        return $this->belongsTo(CompteMarchand::class);
    }

    public function pilgrim()
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function getClientDisplayAttribute(): string
    {
        if ($this->pilgrim_id && $this->pilgrim) {
            return $this->pilgrim->first_name . ' ' . $this->pilgrim->last_name;
        }
        return $this->client_nom ?? '—';
    }
}
