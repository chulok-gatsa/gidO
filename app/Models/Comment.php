<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'excursion_id',
        'content',
        'is_approved',
        'rejection_reason'
    ];
    
    protected $casts = [
        'is_approved' => 'boolean'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function excursion()
    {
        return $this->belongsTo(Excursion::class);
    }
    
    public function getIsRejectedAttribute()
    {
        return !$this->is_approved && !empty($this->rejection_reason);
    }
}