<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'image',
        'address'
    ];
    

    public function getImagePathAttribute()
    {
        return $this->image ? '/storage/' . $this->image : null;
    }
    

    public function excursions()
    {
        return $this->belongsToMany(Excursion::class, 'excursion_places')
            ->withPivot('order');
    }
}