<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Excursion extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'short_description',
        'route_length',
        'duration',
        'type',
        'main_image',
        'audio_preview',
        'audio_demo',
        'audio_full',
        'starting_point',
        'map_embed',
        'sights_list',
        'price'
    ];
    
    protected $casts = [
        'sights_list' => 'array',
    ];
    
    public function getImagePathAttribute()
    {
        return $this->main_image ? '/storage/' . $this->main_image : null;
    }
    
    public function getAudioPreviewPathAttribute()
    {
        return $this->audio_preview ? '/storage/' . $this->audio_preview : null;
    }
    
    public function getDemoAudioPathAttribute()
    {
        return $this->audio_demo ? '/storage/' . $this->audio_demo : null;
    }
    
    public function getAudioPathAttribute()
    {
        return $this->audio_full ? '/storage/' . $this->audio_full : null;
    }

    public function immersive()
    {
        return $this->hasOne(Immersive::class);
    }

    public function places()
    {
        return $this->belongsToMany(Place::class, 'excursion_places')
            ->withPivot('order')
            ->orderBy('order');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('is_approved', true);
    }

    public function purchases()
    {
        return $this->hasMany(ExcursionPurchase::class);
    }

    public function getSightsAttribute()
    {
        return $this->sights_list ? $this->sights_list : [];
    }
}