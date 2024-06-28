<?php

namespace App\Domain\Customer\Services;

use App\Domain\Customer\Repositories\CustomerRepository;
use App\Domain\Order\Repositories\OrderRepository;
use App\Libs\Woocommerce;

class CustomerService
{
    protected $repository;
    protected $woocommerce;
    protected $orderRepository;

    public function __construct(
        CustomerRepository $repository,
        Woocommerce $woocommerce,
        OrderRepository $orderRepository
    ) {
        $this->repository = $repository;
        $this->woocommerce = $woocommerce;
        $this->orderRepository = $orderRepository;
    }

    public function getAllCustomers()
    {
        $page = 1;
        $perPage = 100;
        $allCustomers = [];

        do {
            $customers = $this->woocommerce->get('customers', [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            foreach ($customers as $customer) {
                $allCustomers[] = $this->mapWooCommerceToCustomer($customer);
            }

            $page++;
        } while (count($customers) === $perPage);

        return $allCustomers;
    }

    protected function mapWooCommerceToCustomer($wooCustomer)
    {
        $localCustomer = $this->repository->findByCustomerId($wooCustomer->id);
        
        if (!$localCustomer) {
            $orderCount = $this->getCustomerOrderCount($wooCustomer->id);
            $ltv = $this->calculateCustomerLTV($wooCustomer->id);
        } else {
            $orderCount = $localCustomer->order_count;
            $ltv = $localCustomer->ltv;
        }

        return [
            'customer_id' => $wooCustomer->id,
            'name' => $wooCustomer->first_name . ' ' . $wooCustomer->last_name,
            'email' => $wooCustomer->email,
            'age' => $localCustomer ? $localCustomer->age : null,
            'city' => $wooCustomer->billing->city,
            'order_count' => $orderCount,
            'ltv' => $ltv,
        ];
    }

    protected function getCustomerOrderCount($customerId)
    {
        return $this->orderRepository->getOrderCountByCustomerId($customerId);
    }

    protected function calculateCustomerLTV($customerId)
    {
        return $this->orderRepository->getTotalMarginByCustomerId($customerId);
    }

    public function updateCustomerLTV($customerId)
    {
        $customer = $this->repository->findByCustomerId($customerId);
        if (!$customer) {
            throw new \Exception("Customer not found");
        }

        $ltv = $this->calculateCustomerLTV($customerId);
        $orderCount = $this->getCustomerOrderCount($customerId);

        $updatedCustomer = [
            'ltv' => $ltv,
            'order_count' => $orderCount,
        ];

        $this->repository->update($customer->id, $updatedCustomer);

        return $this->repository->findByCustomerId($customerId);
    }
}
