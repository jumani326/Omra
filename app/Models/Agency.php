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
        'contact',
        'logo',
    ];

    protected function casts(): array
    {
        return [
            'contact' => 'array',
        ];
    }

    // Relations
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('ministry_status', 'approved');
    }
}
