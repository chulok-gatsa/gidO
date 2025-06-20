<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImmersiveTime extends Model
{
    use HasFactory;
    

    protected $fillable = [
        'immersive_date_id',
        'event_time',
        'is_available'
    ];
    

    protected $casts = [
        'is_available' => 'boolean'
    ];
    

    public function date()
    {
        return $this->belongsTo(ImmersiveDate::class, 'immersive_date_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}