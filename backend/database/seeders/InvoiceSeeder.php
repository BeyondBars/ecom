<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['paid', 'pending', 'overdue', 'draft'];
        
        for ($i = 1; $i <= 50; $i++) {
            $status = $statuses[array_rand($statuses)];
            $createdAt = Carbon::now()->subDays(rand(1, 60));
            $dueDate = Carbon::parse($createdAt)->addDays(30);
            
            // If status is overdue, make sure due date is in the past
            if ($status === 'overdue') {
                $dueDate = Carbon::now()->subDays(rand(1, 15));
            }
            
            // If status is paid, add a payment date
            $paidAt = null;
            if ($status === 'paid') {
                $paidAt = Carbon::parse($createdAt)->addDays(rand(1, 25));
            }
            
            $total = rand(10000, 500000) / 100;
            $tax = $total * 0.1;
            $subtotal = $total - $tax;
            
            DB::table('invoices')->insert([
                'invoice_number' => 'INV-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'order_id' => rand(1, 30),
                'user_id' => rand(1, 15),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => $status,
                'due_date' => $dueDate,
                'paid_at' => $paidAt,
                'notes' => $status === 'draft' ? 'Draft invoice - pending review' : 'Thank you for your business!',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            
            // Create 1-5 invoice items for each invoice
            $itemCount = rand(1, 5);
            for ($j = 1; $j <= $itemCount; $j++) {
                $quantity = rand(1, 10);
                $price = rand(1000, 50000) / 100;
                
                DB::table('invoice_items')->insert([
                    'invoice_id' => $i,
                    'product_id' => rand(1, 20),
                    'description' => 'Product ' . rand(1, 100),
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $quantity * $price,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}
