<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class order extends Model
{
    use HasFactory;
    protected $fillable = [
        'custom_id',
        'number',
        'total_price',
        'status',
        'shipping_price',
        'notes',
        'deleted_at'

    ];
    public function custom(): BelongsTo
    {
        return $this->belongsTo(Custom::class, 'custom_id', 'id');
    }
    public function product(): HasMany
    {
        return $this->hasMany(Order_product::class, 'order_id');
    }
}
