<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['pilgrim_id', 'guide_id', 'type'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function pilgrim()
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }
}
