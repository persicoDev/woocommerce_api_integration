<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Domain\Order\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function getAnalytics(Request $request)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = new \DateTime($validatedData['start_date']);
        $endDate = new \DateTime($validatedData['end_date']);

        $analytics = $this->orderService->getOrderAnalytics($startDate, $endDate);
        return response()->json($analytics);
    }
}
