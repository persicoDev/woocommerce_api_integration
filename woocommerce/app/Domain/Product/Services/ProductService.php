<?php


namespace App\Domain\Product\Services;

use App\Domain\Product\Repositories\ProductRepository;
use App\Libs\Woocommerce;

class ProductService
{
    protected $repository;
    protected $woocommerce;

    public function __construct(ProductRepository $repository, Woocommerce $woocommerce)
    {
        $this->repository = $repository;
        $this->woocommerce = $woocommerce;
    }

    public function getAllProducts()
    {
        $page = 1;
        $perPage = 100;
        $allProducts = [];

        do {
            $products = $this->woocommerce->get('products', [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            $this->dispatchUpdateJobs($products);
            
            foreach ($products as $product) {
                $allProducts[] = $this->mapWooCommerceToProduct($product);
            }

            $page++;
        } while (count($products) === $perPage);


        return $allProducts;
    }

    public function getProduct($id)
    {
        $wooProduct = $this->woocommerce->get("products/{$id}");
        return $wooProduct ? $this->mapWooCommerceToProduct($wooProduct) : null;
    }

    public function updateProduct($id, array $data)
    {
        $wooProduct = $this->woocommerce->put("products/{$id}", $data);
        $mappedProduct = $this->mapWooCommerceToProduct($wooProduct);
        $this->repository->updateOrCreate(['product_id' => $id], $mappedProduct);
        return $mappedProduct;
    }

    public function createProduct(array $data)
    {
        $wooProduct = $this->woocommerce->post('products', $data);
        $mappedProduct = $this->mapWooCommerceToProduct($wooProduct);
        $this->repository->create($mappedProduct);
        return $mappedProduct;
    }

    protected function mapWooCommerceToProduct($wooProduct)
    {
        $localProduct = $this->repository->findByProductId($wooProduct->id);
        
        $cost = $localProduct ? $localProduct->cost : 0;
        $unitsSold = $localProduct ? $localProduct->units_sold : 0;
        
        $price = floatval($wooProduct->price);
        $margin = $price - $cost;
        $marginPercentage = $cost > 0 ? ($margin / $cost) * 100 : 0;

        return [
            'product_id' => $wooProduct->id,
            'sku' => $wooProduct->sku,
            'title' => $wooProduct->name,
            'price' => $price,
            'quantity' => $wooProduct->stock_quantity,
            'image_url' => $wooProduct->images[0]->src ?? null,
            'shipping_price' => $wooProduct->shipping_class,
            'category' => $wooProduct->categories[0]->name ?? null,
            'link' => $wooProduct->permalink,
            'cost' => $cost,
            'margin' => $margin,
            'margin_percentage' => $marginPercentage,
            'units_sold' => $unitsSold,
        ];
    }

    public function updateProductCost($productId, $newCost)
    {
        $product = $this->repository->findByProductId($productId);
        if (!$product) {
            throw new \Exception("Product not found");
        }

        $wooProduct = $this->woocommerce->get("products/{$productId}");
        $price = floatval($wooProduct->price);
        $margin = $price - $newCost;
        $marginPercentage = $newCost > 0 ? ($margin / $newCost) * 100 : 0;

        $updatedProduct = [
            'cost' => $newCost,
            'margin' => $margin,
            'margin_percentage' => $marginPercentage,
        ];

        $this->repository->update($product->id, $updatedProduct);

        return $this->repository->findByProductId($productId);
    }
    protected function dispatchUpdateJobs($wooProducts)
    {
        foreach ($wooProducts as $wooProduct) {
            $productData = $this->mapWooCommerceToProduct($wooProduct);
            UpdateMongoDBProduct::dispatch($productData);
        }
    }
}