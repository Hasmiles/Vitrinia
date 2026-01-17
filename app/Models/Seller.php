<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'shop_name',
        'logo',
        'phone',
        'iban',
        'is_completed'
    ];
}
