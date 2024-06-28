<?php

namespace App\Domain\Order\Services;

use App\Domain\Order\Repositories\OrderRepository;
use App\Domain\Product\Repositories\ProductRepository;
use App\Libs\Woocommerce;
use Carbon\Carbon;

class OrderService
{
    protected $repository;
    protected $woocommerce;
    protected $productRepository;

    public function __construct(
        OrderRepository $repository,
        Woocommerce $woocommerce,
        ProductRepository $productRepository
    ) {
        $this->repository = $repository;
        $this->woocommerce = $woocommerce;
        $this->productRepository = $productRepository;
    }

    public function getOrderAnalytics($startDate, $endDate)
    {
        $orders = $this->woocommerce->get('orders', [
            'after' => $startDate->toIso8601String(),
            'before' => $endDate->toIso8601String(),
            'status' => 'completed',
        ]);

        $analytics = [
            'order_count' => count($orders),
            'revenue' => 0,
            'margin' => 0,
        ];

        foreach ($orders as $order) {
            $orderDetails = $this->calculateOrderDetails($order);
            $analytics['revenue'] += $orderDetails['revenue'];
            $analytics['margin'] += $orderDetails['margin'];
        }

        return $analytics;
    }

    protected function calculateOrderDetails($order)
    {
        $revenue = 0;
        $margin = 0;
        $costs = 0;

        foreach ($order->line_items as $item) {
            $product = $this->productRepository->findByProductId($item->product_id);
            
            if (!$product) {
                // Log warning: Product not found in local database
                continue;
            }

            $itemRevenue = $item->total;
            $itemCost = $product->cost * $item->quantity;

            $revenue += $itemRevenue;
            $costs += $itemCost;
            $margin += ($itemRevenue - $itemCost);

            // Update product sales data
            $this->productRepository->incrementSales($product->id, $item->quantity);
        }

        return [
            'revenue' => $revenue,
            'costs' => $costs,
            'margin' => $margin,
        ];
    }
}
