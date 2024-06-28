<?php


namespace App\Domain\Product\Models;

use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'product_id',
        'sku',
        'title',
        'price',
        'quantity',
        'image_url',
        'shipping_price',
        'category',
        'link',
        'cost',
        'margin',
        'margin_percentage',
        'units_sold',
    ];

    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
        'shipping_price' => 'float',
        'cost' => 'float',
        'margin' => 'float',
        'margin_percentage' => 'float',
        'units_sold' => 'integer',
    ];
}
