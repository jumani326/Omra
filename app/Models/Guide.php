<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'agency_id', 'group_id'];

    /**
     * Un guide n'appartient qu'à une seule agence (créé par l'agence).
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function pilgrims()
    {
        return $this->hasMany(Pilgrim::class, 'guide_id');
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class, 'guide_id');
    }

    public function scopeAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }
}
