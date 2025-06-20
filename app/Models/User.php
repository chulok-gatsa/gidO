<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'login',
        'email',
        'password',
        'is_admin',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];
    

    public function isAdmin()
    {
        return $this->is_admin;
    }
    

    public function excursionPurchases()
    {
        return $this->hasMany(ExcursionPurchase::class);
    }
    

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}