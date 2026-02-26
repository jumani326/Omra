<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'name',
        'address',
        'phone',
        'manager_id',
    ];

    // Relations
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function pilgrims()
    {
        return $this->hasMany(Pilgrim::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
