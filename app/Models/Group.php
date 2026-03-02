<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['agency_id', 'name'];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function guide()
    {
        return $this->hasOne(Guide::class);
    }

    public function pilgrims()
    {
        return $this->hasMany(Pilgrim::class, 'group_id');
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class, 'group_id')->orderBy('created_at');
    }

    public function scopeAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }
}
