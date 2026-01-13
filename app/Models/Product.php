<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'seller_id',
        'name',
        'image',
        'price',
        'stock'
    ];

    public function subOptions()
    {
        return $this->belongsToMany(SubOption::class, 'product_options', 'product_id', 'option_id');
    }

    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class, 'product_id', 'id');
    }
}
