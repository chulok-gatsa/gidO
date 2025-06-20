<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ImmersiveDate extends Model
{
    use HasFactory;
    

    protected $fillable = [
        'immersive_id',
        'event_date',
        'is_available'
    ];
    

    protected $casts = [
        'event_date' => 'date',
        'is_available' => 'boolean'
    ];
    

    public function immersive()
    {
        return $this->belongsTo(Immersive::class);
    }
    

    public function times()
    {
        return $this->hasMany(ImmersiveTime::class);
    }
    
    public function availableTimes()
    {
        return $this->hasMany(ImmersiveTime::class)
            ->where('is_available', true)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('bookings')
                      ->whereRaw('bookings.immersive_time_id = immersive_times.id')
                      ->where('bookings.is_paid', true);
            })
            ->orderBy('event_time');
    }
    
    public function allTimes()
    {
        return $this->hasMany(ImmersiveTime::class)
            ->orderBy('event_time');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}