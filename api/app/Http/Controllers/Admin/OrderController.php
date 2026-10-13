<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Shop\OrderManager;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentMethod;
use App\Support\Enums\PaymentStatus;
use App\Support\Enums\ReturnStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        return OrderResource::collection(
            QueryBuilder::for(Order::query()->with('user')->withCount('items'))
                ->allowedFilters(
                    AllowedFilter::exact('status'),
                    AllowedFilter::exact('payment_status'),
                    AllowedFilter::exact('payment_method'),
                    AllowedFilter::exact('return_status'),
                    AllowedFilter::exact('user_id'),
                    AllowedFilter::callback('search', function ($query, $value) {
                        $query->where(fn ($q) => $q
                            ->where('number', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%")
                            ->orWhere('tracking_number', 'like', "%{$value}%"));
                    }),
                    AllowedFilter::callback('from', fn ($q, $value) => $q->whereDate('created_at', '>=', $value)),
                    AllowedFilter::callback('to', fn ($q, $value) => $q->whereDate('created_at', '<=', $value)),
                    // Hide online orders that were never paid unless asked for
                    AllowedFilter::callback('include_unpaid', fn ($q, $value) => $q),
                )
                ->when(!request()->boolean('filter.include_unpaid'), fn ($q) => $q->where(fn ($q) => $q
                    ->where('payment_method', '!=', PaymentMethod::Razorpay->value)
                    ->orWhere('payment_status', '!=', PaymentStatus::Pending->value)
                    ->orWhere('status', '!=', OrderStatus::Pending->value)))
                ->allowedSorts('created_at', 'total', 'number')
                ->defaultSort('-created_at')
                ->paginate(min((int) request('per_page', 15), 100))
        );
    }

    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return new OrderResource($order->load('items', 'events.user', 'user'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderManager $orders): OrderResource
    {
        $orders->updateStatus(
            $order,
            OrderStatus::from($request->validated('status')),
            $request->user(),
            $request->safe()->only(['note', 'courier', 'tracking_number']),
        );

        return $this->show($order->refresh());
    }

    /**
     * Record cash collected for a Cash on Delivery order (delivery also does this automatically).
     */
    public function markPaid(Request $request, Order $order, OrderManager $orders): OrderResource
    {
        $this->authorize('update', $order);

        if ($order->payment_method !== PaymentMethod::CashOnDelivery || $order->payment_status !== PaymentStatus::Pending) {
            throw ValidationException::withMessages(['payment_status' => 'Only unpaid Cash on Delivery orders can be marked as paid here.']);
        }

        $data = $request->validate(['reference' => ['nullable', 'string', 'max:100']]);
        $orders->markPaid($order, $data['reference'] ?? 'Cash on delivery', $request->user());

        return $this->show($order->refresh());
    }

    public function resolveReturn(Request $request, Order $order, OrderManager $orders): OrderResource
    {
        $this->authorize('update', $order);

        $data = $request->validate([
            'decision' => ['required', Rule::enum(ReturnStatus::class)->except([ReturnStatus::Requested])],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $orders->resolveReturn($order, ReturnStatus::from($data['decision']), $data['note'] ?? null, $request->user());

        return $this->show($order->refresh());
    }

    public function markRefunded(Request $request, Order $order, OrderManager $orders): OrderResource
    {
        $this->authorize('update', $order);

        $data = $request->validate(['reference' => ['nullable', 'string', 'max:100']]);
        $orders->markRefunded($order, $data['reference'] ?? null, $request->user());

        return $this->show($order->refresh());
    }

    public function addNote(Request $request, Order $order, OrderManager $orders): OrderResource
    {
        $this->authorize('update', $order);

        $data = $request->validate(['note' => ['required', 'string', 'max:1000']]);
        $orders->addNote($order, $data['note'], $request->user());

        return $this->show($order->refresh());
    }
}
