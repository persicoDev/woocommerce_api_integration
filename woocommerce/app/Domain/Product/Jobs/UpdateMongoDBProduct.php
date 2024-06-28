<?php

namespace App\Domain\Product\Jobs;

use App\Domain\Product\Repositories\ProductRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateMongoDBProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productData;

    public function __construct(array $productData)
    {
        $this->productData = $productData;
    }

    public function handle(ProductRepository $repository)
    {
        $mongoProduct = $repository->find($this->productData['id']);

        if ($mongoProduct && $this->isPriceDifferent($this->productData, $mongoProduct->toArray())) {
            try {
                $repository->updatePrice($this->productData['id'], $this->productData['price']);
                Log::info("Updated price for product ID {$this->productData['id']}");
            } catch (\Exception $e) {
                Log::error("Failed to update price for product ID {$this->productData['id']}: " . $e->getMessage());
            }
        }
    }

    protected function isPriceDifferent($wooProduct, $mongoProduct)
    {
        return $wooProduct['price'] != $mongoProduct['price'];
    }
}