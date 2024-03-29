<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

class Custom extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'date_of_birth',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'custom_id');
    }
    public function addresse(): HasMany
    {
        return $this->hasMany(Addresse::class, 'user_id');
    }
    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class, 'user_id');
    }
    
    public function reviews(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'user_id');
    }
    public function favoriteProducts()
    {
        return $this->belongsToMany(product::class, 'favourtes','user_id');
    }

}
