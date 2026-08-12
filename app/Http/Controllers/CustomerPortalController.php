<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\PostponementRequest;
use Illuminate\Http\Request;

class CustomerPortalController extends Controller
{
    public function show($token = null)
    {
        if (empty($token)) {
            abort(404, 'رمز بوابة العميل غير متوفر.');
        }

        $customer = Customer::where('access_token', $token)
            ->with(['installmentPlans.installments.latestPostponementRequest'])
            ->firstOrFail();

        $totalAmount = 0;
        $totalPaid = 0;
        $allInstallments = collect();

        foreach ($customer->installmentPlans as $plan) {
            $totalAmount += $plan->total_amount;
            $totalPaid += $plan->down_payment;

            foreach ($plan->installments as $installment) {
                if ($installment->is_paid) {
                    $totalPaid += $installment->amount;
                }
                $allInstallments->push($installment);
            }
        }

        $remainingAmount = max(0, $totalAmount - $totalPaid);
        $allInstallments = $allInstallments->sortBy('due_date');

        return view('customer_portal.index', compact(
            'customer',
            'totalAmount',
            'totalPaid',
            'remainingAmount',
            'allInstallments'
        ));
    }

    public function requestPostponement(Request $request, $token = null)
    {
        if (empty($token)) {
            abort(404, 'رمز بوابة العميل غير متوفر.');
        }

        $customer = Customer::where('access_token', $token)->firstOrFail();

        $request->validate([
            'installment_id' => 'required|exists:installments,id',
            'requested_due_date' => 'required|date|after:today',
            'reason' => 'nullable|string|max:500',
        ]);

        $installment = Installment::where('id', $request->installment_id)
            ->whereHas('plan', function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->firstOrFail();

        if ($installment->is_paid) {
            return back()->with('error', 'هذا القسط مدفوع بالفعل ولا يمكن طلب تأجيله.');
        }

        // Check if there is already a pending request
        $existingPending = PostponementRequest::where('installment_id', $installment->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return back()->with('error', 'يوجد طلب تأجيل معلق بالفعل لهذا القسط.');
        }

        PostponementRequest::create([
            'customer_id' => $customer->id,
            'installment_id' => $installment->id,
            'requested_due_date' => $request->requested_due_date,
            'reason' => $request->reason,
            'status' => 'pending',
            'extra_interest' => 0,
        ]);

        return back()->with('success', 'تم تقديم طلب التأجيل بنجاح، سيتم مراجعته من قبل صاحب المحل.');
    }
}
