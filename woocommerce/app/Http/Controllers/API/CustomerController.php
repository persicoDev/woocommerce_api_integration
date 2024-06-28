<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Domain\Customer\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        return response()->json($this->customerService->getAllCustomers());
    }

    public function updateLTV($id)
    {
        try {
            $customer = $this->customerService->updateCustomerLTV($id);
            return response()->json($customer);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}

