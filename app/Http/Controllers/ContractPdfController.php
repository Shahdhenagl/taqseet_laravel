<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\InstallmentPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ContractPdfController extends Controller
{
    public function printContract($id)
    {
        $plan = InstallmentPlan::with(['customer', 'installments'])->findOrFail($id);

        if (request()->has('html')) {
            return view('pdf.contract', compact('plan'));
        }

        $pdf = Pdf::loadView('pdf.contract', compact('plan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("contract_{$plan->id}.pdf");
    }

    public function printReceipt($id)
    {
        $installment = Installment::with(['plan.customer'])->findOrFail($id);

        if (request()->has('html')) {
            return view('pdf.receipt', compact('installment'));
        }

        $pdf = Pdf::loadView('pdf.receipt', compact('installment'))
            ->setPaper('a5', 'landscape');

        return $pdf->stream("receipt_{$installment->id}.pdf");
    }
}
