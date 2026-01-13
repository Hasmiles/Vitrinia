<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'fullname',
        'phone',
        'city',
        'town',
        'address',
        'address_2',
        'note',
        'active'
    ];
}
