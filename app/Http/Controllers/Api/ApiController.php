<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Installment;
use App\Models\Product;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'pending_installments_count' => Installment::where('is_paid', false)->count(),
                'products_count' => Product::count(),
                'customers_count' => Customer::count(),
            ]
        ]);
    }

    public function products()
    {
        return response()->json([
            'status' => 'success',
            'data' => Product::orderBy('created_at', 'desc')->get()
        ]);
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة المنتج بنجاح',
            'data' => $product
        ], 201);
    }

    public function customers()
    {
        $customers = Customer::with(['installmentPlans.installments'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $customers
        ]);
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة العميل بنجاح',
            'data' => $customer
        ], 201);
    }
}
