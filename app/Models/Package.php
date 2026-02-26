<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'type',
        'price',
        'cost',
        'slots',
        'slots_remaining',
        'departure_date',
        'return_date',
        'hotel_mecca_id',
        'hotel_medina_id',
        'nights_mecca',
        'nights_medina',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'departure_date' => 'date',
            'return_date' => 'date',
        ];
    }

    // Relations
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function hotelMecca()
    {
        return $this->belongsTo(Hotel::class, 'hotel_mecca_id');
    }

    public function hotelMedina()
    {
        return $this->belongsTo(Hotel::class, 'hotel_medina_id');
    }

    public function pilgrims()
    {
        return $this->hasMany(Pilgrim::class);
    }

    // Scopes
    public function scopeBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAvailable($query)
    {
        return $query->where('slots_remaining', '>', 0);
    }
}
