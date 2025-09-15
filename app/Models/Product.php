<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'cost_price',
        'sale_price',
        'pix_price',
        'stock_notice',
        'is_active',
    ];
}
