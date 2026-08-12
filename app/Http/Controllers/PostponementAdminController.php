<?php

namespace App\Http\Controllers;

use App\Models\PostponementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostponementAdminController extends Controller
{
    public function index()
    {
        $requests = PostponementRequest::with(['customer', 'installment.plan'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingCount = PostponementRequest::where('status', 'pending')->count();

        return view('admin.postponements.index', compact('requests', 'pendingCount'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'extra_interest' => 'nullable|numeric|min:0',
        ]);

        $extraInterest = (float) ($request->extra_interest ?? 0);

        DB::transaction(function () use ($id, $extraInterest) {
            $postponement = PostponementRequest::with('installment.plan')->findOrFail($id);

            if ($postponement->status !== 'pending') {
                throw new \Exception('هذا الطلب تم تمت معالجته سابقاً.');
            }

            $installment = $postponement->installment;
            $plan = $installment->plan;

            // Update Installment
            $installment->due_date = $postponement->requested_due_date;
            $installment->amount += $extraInterest;
            $installment->save();

            // Update Plan amounts if extra interest added
            if ($extraInterest > 0 && $plan) {
                $plan->total_amount += $extraInterest;
                $plan->remaining_amount += $extraInterest;
                $plan->save();
            }

            // Update Request Status
            $postponement->status = 'approved';
            $postponement->extra_interest = $extraInterest;
            $postponement->save();
        });

        return back()->with('success', 'تم قبول طلب التأجيل وتحديث القسط وإجمالي التكلفة بنجاح.');
    }

    public function reject(Request $request, $id)
    {
        $postponement = PostponementRequest::findOrFail($id);

        if ($postponement->status !== 'pending') {
            return back()->with('error', 'هذا الطلب تمت معالجته سابقاً.');
        }

        $postponement->status = 'rejected';
        $postponement->save();

        return back()->with('success', 'تم رفض طلب التأجيل.');
    }
}
