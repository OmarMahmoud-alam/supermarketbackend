<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable=[
        'alt',
        'image',
        'isvisible',
        'src',
        
    ];
    use HasFactory;
}
