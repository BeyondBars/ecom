<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Support\Enums\ModerationStatus;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\ReturnStatus;
use App\Support\Enums\UserAbility;
use App\Support\Enums\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        abort_unless($request->user()->can(UserAbility::ViewReports->value), 403);

        $days = (int) ($request->validate(['days' => ['nullable', 'integer', 'in:7,30,90,365']])['days'] ?? 30);
        $from = now()->subDays($days - 1)->startOfDay();
        $previousFrom = $from->copy()->subDays($days);

        $placed = fn () => Order::query()->placed();

        $current = $placed()->where('created_at', '>=', $from);
        $previous = $placed()->whereBetween('created_at', [$previousFrom, $from]);

        $revenue = (float) $current->clone()->sum('total');
        $orders = $current->clone()->count();

        // Revenue per day for the chart (days without sales show as 0)
        $daily = $current->clone()
            ->selectRaw('DATE(created_at) as day, SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $series = collect(range(0, $days - 1))->map(function ($offset) use ($from, $daily) {
            $day = $from->copy()->addDays($offset)->toDateString();

            return [
                'date' => $day,
                'revenue' => (float) ($daily[$day]->revenue ?? 0),
                'orders' => (int) ($daily[$day]->orders ?? 0),
            ];
        });

        $topProducts = OrderItem::query()
            ->whereIn('order_id', $current->clone()->select('id'))
            ->selectRaw('product_id, name, SUM(quantity) as units, SUM(total) as revenue')
            ->groupBy('product_id', 'name')
            ->orderByDesc('units')
            ->limit(5)
            ->get();

        return response()->json(['data' => [
            'period_days' => $days,
            'revenue' => $revenue,
            'revenue_previous' => (float) $previous->clone()->sum('total'),
            'orders' => $orders,
            'orders_previous' => $previous->clone()->count(),
            'average_order_value' => $orders ? round($revenue / $orders, 2) : 0,
            'new_customers' => User::whereIs(UserRole::User->value)->where('created_at', '>=', $from)->count(),
            'series' => $series,
            'top_products' => $topProducts,
            'to_do' => [
                'to_ship' => Order::placed()->whereIn('status', [OrderStatus::Pending->value, OrderStatus::Processing->value])->count(),
                'returns' => Order::whereIn('return_status', [ReturnStatus::Requested->value, ReturnStatus::Approved->value])->count(),
                'refunds' => Order::where('payment_status', 'refund_pending')->count(),
                'reviews' => Review::where('status', ModerationStatus::Pending->value)->count(),
                'low_stock' => Product::where('status', 'active')->where('stock', '<=', Product::LOW_STOCK_THRESHOLD)->count(),
            ],
            'recent_orders' => OrderResource::collection(Order::placed()->with('user')->withCount('items')->latest('id')->limit(5)->get()),
        ]]);
    }
}
