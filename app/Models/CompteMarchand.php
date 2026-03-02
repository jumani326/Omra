<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompteMarchand extends Model
{
    use HasFactory;

    protected $table = 'compte_marchands';

    public const METHODES = ['D-money', 'Waafi', 'MyCac'];

    protected $fillable = [
        'nom_methode',
        'numero_compte',
        'nom_agence',
        'branch_id',
        'solde',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'solde' => 'decimal:2',
            'actif' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactionsDigitales()
    {
        return $this->hasMany(TransactionDigitale::class, 'compte_marchand_id');
    }

    public function scopePourBranch($query, $branchId)
    {
        if ($branchId === null) {
            return $query->whereNull('branch_id');
        }
        return $query->where('branch_id', $branchId);
    }

    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }
}
