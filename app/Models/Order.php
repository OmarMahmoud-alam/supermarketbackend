<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class order extends Model
{
    use HasFactory;
    protected $appends = ['total_price2','product_order'];

    protected $fillable = [
        'custom_id',
        'number',
        'total_price',
        'delivery_type',
        'address_id',
        'status',
        'shipping_price',
        'notes',
        'deleted_at'

    ];
    public function custom(): BelongsTo
    {
        return $this->belongsTo(Custom::class, 'custom_id', 'id');
    }
    public function ordersproduct(): HasMany
    {
        return $this->hasMany(Order_product::class, 'order_id');
    }
    public function addresse(): HasOne
    {
        return $this->hasOne(Addresse::class, 'address_id');
    }
    public function getTotalPrice2Attribute()
    {
        $totalProductPrice = $this->ordersproduct->sum(function ($product) {
            return $product->quantity * $product->unit_price;
        });

        return $totalProductPrice + $this->shipping_price;
    }
    public function getProductOrderAttribute()
    {
        if ($this->ordersproduct->isEmpty()) {
            return 'No products';
        }

        $formattedProducts = $this->ordersproduct->map(function ($orderProduct) {
            $product = $orderProduct->product;

            if ($product) {
                return $product->name . ' (' . $orderProduct->unit_price . ')';
            }

        });

        return $formattedProducts;      
    }
}
