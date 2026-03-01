<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pilgrim extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'branch_id',
        'agence_id',
        'group_id',
        'guide_id',
        'agent_id',
        'package_id',
        'passport_no',
        'first_name',
        'last_name',
        'email',
        'phone',
        'nationality',
        'status',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agence_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function visa()
    {
        return $this->hasOne(Visa::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function documents()
    {
        return $this->hasMany(PilgrimDocument::class);
    }

    public function chatSession()
    {
        return $this->hasOne(ChatSession::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }

    // Scopes
    public function scopeBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeAgency($query, $agencyId)
    {
        return $query->where('agence_id', $agencyId);
    }

    public function scopeGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }
}
