<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Addresse extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'user_id',
        'street_address',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_default',
        
    ];
    public function custom(): BelongsTo
    {
        return $this->belongsTo(Custom::class, 'custom_id', 'id');
    }
}
