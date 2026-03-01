<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'license_no',
        'ministry_status',
        'validated',
        'validated_by',
        'validated_at',
        'contact',
        'logo',
    ];

    protected function casts(): array
    {
        return [
            'contact' => 'array',
            'validated' => 'boolean',
            'validated_at' => 'datetime',
        ];
    }

    // Relations
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function validatedByUser()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'agency_id');
    }

    public function guides()
    {
        return $this->hasMany(Guide::class, 'agency_id');
    }

    public function pilgrims()
    {
        return $this->hasMany(Pilgrim::class, 'agence_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'agence_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('ministry_status', 'approved');
    }
}
