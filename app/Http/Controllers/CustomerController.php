<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['installmentPlans.installments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->get()->map(function ($customer) {
            $hasLate = $customer->installmentPlans->flatMap->installments->contains(function ($installment) {
                return !$installment->is_paid && $installment->due_date->isPast();
            });
            $customer->hasLateInstallments = $hasLate;

            $totalContracts = $customer->installmentPlans->sum('total_amount');
            $remainingBalance = $customer->installmentPlans->sum('remaining_amount');
            $customer->totalContractsAmount = $totalContracts;
            $customer->remainingBalance = $remainingBalance;

            return $customer;
        });

        return view('customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customer::with(['installmentPlans.installments.latestPostponementRequest', 'invoices.items.product'])
            ->findOrFail($id);

        $hasLate = $customer->installmentPlans->flatMap->installments->contains(function ($installment) {
            return !$installment->is_paid && $installment->due_date->isPast();
        });
        $customer->hasLateInstallments = $hasLate;

        $totalAmount = 0;
        $totalPaid = 0;

        foreach ($customer->installmentPlans as $plan) {
            $totalAmount += $plan->total_amount;
            $totalPaid += $plan->down_payment;
            foreach ($plan->installments as $inst) {
                if ($inst->is_paid) {
                    $totalPaid += $inst->amount;
                }
            }
        }

        $remainingAmount = max(0, $totalAmount - $totalPaid);

        return view('customers.show', compact(
            'customer',
            'totalAmount',
            'totalPaid',
            'remainingAmount'
        ));
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
