<?php

namespace App\Domain\Order\Models;

use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'order_id',
        'customer_id',
        'date',
        'total',
        'margin',
    ];

    protected $casts = [
        'date' => 'datetime',
        'total' => 'float',
        'margin' => 'float',
    ];
}
