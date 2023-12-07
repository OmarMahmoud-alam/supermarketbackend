<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Favourte extends Model
{   
    protected $primaryKey = ['user_id', 'product_id'];
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
    ];
    public function product(): BelongsTo
    {
        return $this->belongsTo(product::class, 'product_id', 'id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(Custom::class, 'user_id', 'id');
    }
}
