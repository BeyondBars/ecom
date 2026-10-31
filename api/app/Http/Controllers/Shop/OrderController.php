<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Shop\OrderManager;
use Illuminate\Http\Request;

/**
 * The signed-in customer's own orders.
 */
class OrderController extends Controller
{
    public function index(Request $request)
    {
        return OrderResource::collection(
            Order::forCustomer($request->user())
                ->with('items.product:id,slug')
                ->latest('id')
                ->paginate(10)
        );
    }

    public function show(Request $request, Order $order): OrderResource
    {
        $this->ensureOwner($request, $order);

        return new OrderResource($order->load('items.product:id,slug', 'events'));
    }

    public function cancel(Request $request, Order $order, OrderManager $orders): OrderResource
    {
        $this->ensureOwner($request, $order);
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:255']]);

        $orders->cancel($order, $data['reason'] ?? 'Cancelled by customer', $request->user());

        return new OrderResource($order->load('items.product:id,slug', 'events'));
    }

    public function requestReturn(Request $request, Order $order, OrderManager $orders): OrderResource
    {
        $this->ensureOwner($request, $order);
        $data = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:1000']]);

        $orders->requestReturn($order, $data['reason'], $request->user());

        return new OrderResource($order->load('items.product:id,slug', 'events'));
    }

    /**
     * 404 rather than 403 so order numbers can't be probed.
     */
    protected function ensureOwner(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 404);
    }
}
