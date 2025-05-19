<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Invoice::class, 'invoice');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->has('from_date')) {
            $query->whereDate('issue_date', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('issue_date', '<=', $request->input('to_date'));
        }

        if ($request->has('overdue')) {
            $query->where('status', 'pending')
                  ->where('due_date', '<', now());
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'issue_date');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $invoices = $query->paginate($perPage);

        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvoiceStoreRequest $request)
    {
        $data = $request->validated();
        
        // Generate invoice number
        $data['number'] = 'INV-' . strtoupper(uniqid());
        
        DB::beginTransaction();
        
        try {
            // Create invoice
            $invoice = Invoice::create($data);
            
            // Create invoice items
            $subtotal = 0;
            
            foreach ($data['items'] as $item) {
                $invoiceItem = new InvoiceItem([
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                ]);
                
                $invoice->items()->save($invoiceItem);
                $subtotal += $invoiceItem->subtotal;
            }
            
            // Update invoice totals
            $tax = $subtotal * ($data['tax_rate'] ?? 0.1); // Default tax rate 10%
            $total = $subtotal + $tax;
            
            $invoice->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);
            
            // If this invoice is for an order, update the order
            if (isset($data['order_id'])) {
                $order = Order::findOrFail($data['order_id']);
                $order->update(['payment_status' => 'invoiced']);
            }
            
            DB::commit();
            
            $invoice->load(['customer', 'items.product']);
            
            return response()->json($invoice, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'order']);
        
        return response()->json($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InvoiceUpdateRequest $request, Invoice $invoice)
    {
        $data = $request->validated();
        
        DB::beginTransaction();
        
        try {
            // Update invoice
            $invoice->update($data);
            
            // If items are provided, update them
            if (isset($data['items'])) {
                // Delete existing items
                $invoice->items()->delete();
                
                // Create new items
                $subtotal = 0;
                
                foreach ($data['items'] as $item) {
                    $invoiceItem = new InvoiceItem([
                        'product_id' => $item['product_id'] ?? null,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $item['quantity'] * $item['price'],
                    ]);
                    
                    $invoice->items()->save($invoiceItem);
                    $subtotal += $invoiceItem->subtotal;
                }
                
                // Update invoice totals
                $tax = $subtotal * ($data['tax_rate'] ?? 0.1); // Default tax rate 10%
                $total = $subtotal + $tax;
                
                $invoice->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                ]);
            }
            
            DB::commit();
            
            $invoice->load(['customer', 'items.product']);
            
            return response()->json($invoice);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to update invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        DB::beginTransaction();
        
        try {
            // Delete invoice items
            $invoice->items()->delete();
            
            // If this invoice is for an order, update the order
            if ($invoice->order) {
                $invoice->order->update(['payment_status' => 'pending']);
            }
            
            // Delete invoice
            $invoice->delete();
            
            DB::commit();
            
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to delete invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a PDF for the invoice.
     */
    public function generatePdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        $invoice->load(['customer', 'items.product', 'order']);
        
        // Generate PDF using a PDF library like DOMPDF
        $pdf = \PDF::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download('invoice-' . $invoice->number . '.pdf');
    }
}
