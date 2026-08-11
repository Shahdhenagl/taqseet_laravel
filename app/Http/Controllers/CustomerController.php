<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['installmentPlans.installments'])->get()->map(function ($customer) {
            $hasLate = $customer->installmentPlans->flatMap->installments->contains(function ($installment) {
                return !$installment->is_paid && $installment->due_date->isPast();
            });
            $customer->hasLateInstallments = $hasLate;
            return $customer;
        });

        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'تم إضافة العميل بنجاح');
    }
}
