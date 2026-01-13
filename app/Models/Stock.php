<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = [
        'description',
        'product_id',
        'quantity',
    ];

    public function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    /**
     * Get the product that owns the stock.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if stock is low (below minimum quantity)
     */
    public function isLowStock(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->quantity <= data_get($this->product, 'stock_notice')
        );
    }
}
