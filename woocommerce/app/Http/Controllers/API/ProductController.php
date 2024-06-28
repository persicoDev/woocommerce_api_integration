<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Domain\Product\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        return response()->json($this->productService->getAllProducts());
    }

    public function show($id)
    {
        $product = $this->productService->getProduct($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'regular_price' => 'required|numeric',
            'description' => 'required|string',
            'short_description' => 'required|string',
            'categories' => 'array',
            'images' => 'array',
        ]);

        $product = $this->productService->createProduct($validatedData);
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'string',
            'type' => 'string',
            'regular_price' => 'numeric',
            'description' => 'string',
            'short_description' => 'string',
            'categories' => 'array',
            'images' => 'array',
        ]);

        $product = $this->productService->updateProduct($id, $validatedData);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    public function updateCost(Request $request, $id)
    {
        $validatedData = $request->validate([
            'cost' => 'required|numeric',
        ]);

        try {
            $product = $this->productService->updateProductCost($id, $validatedData['cost']);
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}
