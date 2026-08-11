<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingInstallmentsCount = Installment::where('is_paid', false)
            ->where('due_date', '<=', now()->addDays(7))
            ->count();

        $productsCount = Product::count();
        $customersCount = Customer::count();

        return view('dashboard', compact('pendingInstallmentsCount', 'productsCount', 'customersCount'));
    }
}
