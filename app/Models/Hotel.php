<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'name',
        'city',
        'stars',
        'distance_haram',
        'main_image',
        'room_images',
    ];

    protected function casts(): array
    {
        return [
            'distance_haram' => 'decimal:2',
            'room_images' => 'array',
        ];
    }

    // Relations
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function packagesMecca()
    {
        return $this->hasMany(Package::class, 'hotel_mecca_id');
    }

    public function packagesMedina()
    {
        return $this->hasMany(Package::class, 'hotel_medina_id');
    }

    // Scopes
    public function scopeCity($query, $city)
    {
        return $query->where('city', $city);
    }
}
