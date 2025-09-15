<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComboProduct extends Model
{
    protected $table = 'combo_products';

    protected $fillable = [
        'combo_id',
        'product_id',
        'quantity',
    ];

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
