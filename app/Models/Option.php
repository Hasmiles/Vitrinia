<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'title',
        'active'
    ];

    public function sub_option(){
        return $this->hasMany(SubOption::class, 'main_id', 'id');
    }
}
