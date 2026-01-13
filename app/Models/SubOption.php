<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubOption extends Model
{
    protected $fillable = [
        'main_id',
        'title',
        'value',
        'color_hex',
        'active'
    ];

    public function mainOption()
    {
        return $this->belongsTo(Option::class, 'main_id');
    }
}
