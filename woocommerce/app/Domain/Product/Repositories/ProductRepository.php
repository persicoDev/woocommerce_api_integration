<?php

namespace App\Domain\Product\Repositories;

use App\Domain\Product\Models\Product;

class ProductRepository
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function findByProductId($productId)
    {
        return $this->model->where('product_id', $productId)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $product = $this->model->find($id);
        if ($product) {
            $product->update($data);
            return $product;
        }
        return null;
    }

    public function updateOrCreate(array $attributes, array $values)
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function incrementSales($productId, $quantity)
    {
        return $this->model->where('product_id', $productId)->increment('units_sold', $quantity);
    }
}
