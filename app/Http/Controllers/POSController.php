<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InstallmentPlan;
use App\Models\Installment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();
        $customers = Customer::all();
        return view('pos.index', compact('products', 'customers'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_type' => 'required|in:cash,installment',
            'customer_id' => 'nullable|required_if:payment_type,installment|exists:customers,id',
            'down_payment' => 'nullable|numeric|min:0',
            'months' => 'nullable|integer|min:1|max:36',
        ]);

        DB::transaction(function () use ($request) {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id']);
                $quantity = $item['quantity'];
                
                if ($product->stock < $quantity) {
                    throw new \Exception("المخزون غير كافٍ للمنتج: {$product->name}");
                }

                $product->decrement('stock', $quantity);
                $totalAmount += $product->price * $quantity;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ];
            }

            // Create Invoice
            $invoice = Invoice::create([
                'total_amount' => $totalAmount,
                'is_paid' => $request->payment_type === 'cash',
                'customer_id' => $request->customer_id,
            ]);

            foreach ($itemsData as $item) {
                $invoice->items()->create($item);
            }

            // If Installment Plan
            if ($request->payment_type === 'installment') {
                $downPayment = $request->down_payment ?? 0;
                $remaining = $totalAmount - $downPayment;
                $months = $request->months ?? 6;

                $plan = InstallmentPlan::create([
                    'total_amount' => $totalAmount,
                    'down_payment' => $downPayment,
                    'remaining_amount' => $remaining,
                    'customer_id' => $request->customer_id,
                ]);

                $monthlyAmount = $remaining / $months;
                for ($i = 1; $i <= $months; $i++) {
                    Installment::create([
                        'plan_id' => $plan->id,
                        'amount' => $monthlyAmount,
                        'due_date' => now()->addMonths($i),
                        'is_paid' => false,
                    ]);
                }
            }
        });

        return redirect()->route('pos.index')->with('success', 'تم إتمام عملية البيع بنجاح');
    }
}
