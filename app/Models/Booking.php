<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'immersive_id',
        'immersive_date_id',
        'immersive_time_id',
        'name',
        'email',
        'starting_point',
        'people_count',
        'total_price',
        'is_paid'
    ];
    
    protected $casts = [
        'is_paid' => 'boolean'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function immersive()
    {
        return $this->belongsTo(Immersive::class);
    }
    
    public function date()
    {
        return $this->belongsTo(ImmersiveDate::class, 'immersive_date_id');
    }
    
    public function time()
    {
        return $this->belongsTo(ImmersiveTime::class, 'immersive_time_id');
    }
}