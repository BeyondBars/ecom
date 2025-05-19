<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with('user');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $orders = $query->paginate($perPage);

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStoreRequest $request)
    {
        $data = $request->validated();
        
        // Generate order number
        $data['number'] = 'ORD-' . strtoupper(uniqid());
        
        // Calculate totals
        $subtotal = 0;
        
        DB::beginTransaction();
        
        try {
            // Create order
            $order = Order::create($data);
            
            // Create order items
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $orderItem = new OrderItem([
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'options' => $item['options'] ?? null,
                    'subtotal' => $product->price * $item['quantity'],
                ]);
                
                $order->items()->save($orderItem);
                $subtotal += $orderItem->subtotal;
                
                // Update product quantity
                $product->decrement('quantity', $item['quantity']);
            }
            
            // Update order totals
            $tax = $subtotal * ($data['tax_rate'] ?? 0.1); // Default tax rate 10%
            $total = $subtotal + $tax + ($data['shipping'] ?? 0) - ($data['discount'] ?? 0);
            
            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);
            
            DB::commit();
            
            $order->load(['user', 'items.product']);
            
            return response()->json($order, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderUpdateRequest $request, Order $order)
    {
        $data = $request->validated();
        
        $order->update($data);
        $order->load(['user', 'items.product']);

        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Check if the order has an invoice
        if ($order->invoice) {
            return response()->json([
                'message' => 'Cannot delete order with associated invoice.'
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            // Restore product quantities
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('quantity', $item->quantity);
                }
            }
            
            // Delete order items
            $order->items()->delete();
            
            // Delete order
            $order->delete();
            
            DB::commit();
            
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to delete order: ' . $e->getMessage()
            ], 500);
        }
    }
}
