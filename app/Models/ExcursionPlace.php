<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcursionPlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'excursion_id',
        'place_id',
        'order',
    ];

    public function excursion()
    {
        return $this->belongsTo(Excursion::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
