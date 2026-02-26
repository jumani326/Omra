<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'active',
        '2fa_secret',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        '2fa_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    // Relations
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function pilgrims()
    {
        return $this->hasMany(Pilgrim::class, 'agent_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'processed_by');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'agent_id');
    }

    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class, 'escalated_to');
    }

    // Scopes
    public function scopeBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
