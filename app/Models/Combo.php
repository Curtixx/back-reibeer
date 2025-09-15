<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    protected $fillable = ['name', 'sale_price', 'pix_price', 'is_active'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'combo_products');
    }

    public function comboProducts()
    {
        return $this->hasMany(ComboProduct::class);
    }
}
