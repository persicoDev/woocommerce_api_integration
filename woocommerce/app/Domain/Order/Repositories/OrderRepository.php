<?php

namespace App\Domain\Order\Repositories;

use App\Domain\Order\Models\Order;

class OrderRepository
{
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function getOrderCountByCustomerId($customerId)
    {
        return $this->model->where('customer_id', $customerId)->count();
    }

    public function getTotalMarginByCustomerId($customerId)
    {
        return $this->model->where('customer_id', $customerId)->sum('margin');
    }
}