<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'bar_code',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }
}
