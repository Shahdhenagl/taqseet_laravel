<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function pay($id)
    {
        $installment = Installment::findOrFail($id);
        $installment->update([
            'is_paid' => true,
            'paid_date' => now(),
        ]);

        return back()->with('success', 'تم تسجيل سداد القسط بنجاح');
    }
}
