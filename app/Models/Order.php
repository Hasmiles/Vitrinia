<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'seller_id',
        'customer_id',
        'type',
        'status',
        'short_code',
        'barcode'
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->barcode)) {
                $latestBarcode = static::max('barcode');
                $model->barcode = $latestBarcode ? $latestBarcode + 1 : 2700000000000;
            }
        });
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id', 'id');
    }

    public function logs()
    {
        return $this->belongsToMany(Status::class, 'order_logs', 'order_id', 'status')->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
