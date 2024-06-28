<?php

namespace App\Domain\Customer\Repositories;

use App\Domain\Customer\Models\Customer;

class CustomerRepository
{
    protected $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    public function findByCustomerId($customerId)
    {
        return $this->model->where('customer_id', $customerId)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $customer = $this->model->find($id);
        if ($customer) {
            $customer->update($data);
            return $customer;
        }
        return null;
    }
}
