<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'url',
        'main_image'
    ];
    

    public function getImagePathAttribute()
    {
        return $this->main_image ? '/storage/' . $this->main_image : null;
    }
    
    public function getSubtitleAttribute()
    {
        return \Illuminate\Support\Str::limit(strip_tags($this->description), 100);
    }
}