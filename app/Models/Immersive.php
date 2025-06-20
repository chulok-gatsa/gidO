<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Immersive extends Model
{
    use HasFactory;
    

    protected $fillable = [
        'excursion_id',
        'main_description',
        'additional_description',
        'price_per_person',
        'audio_demo',
        'audio_preview'
    ];
    

    public function excursion()
    {
        return $this->belongsTo(Excursion::class);
    }
    

    public function dates()
    {
        return $this->hasMany(ImmersiveDate::class);
    }
    

    public function availableDates()
    {
        return $this->hasMany(ImmersiveDate::class)
            ->where('event_date', '>=', now()->format('Y-m-d'))
            ->where('is_available', true)
            ->orderBy('event_date');
    }
    

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}