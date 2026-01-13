<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProductOption extends Model
{
    protected $fillable = [
        "order_product_id",
        "option_id"
    ];
}
