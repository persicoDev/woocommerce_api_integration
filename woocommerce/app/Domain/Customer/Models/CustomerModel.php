<?php

namespace App\Domain\Customer\Models;

use MongoDB\Laravel\Eloquent\Model;

class Customer extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'customers';

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'age',
        'city',
        'order_count',
        'ltv',
    ];

    protected $casts = [
        'age' => 'integer',
        'order_count' => 'integer',
        'ltv' => 'float',
    ];
}
