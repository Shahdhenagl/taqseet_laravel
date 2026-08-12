@extends('layouts.app')

@section('content')
<div style="padding-top: 16px; margin-bottom: 20px;">
    <a href="{{ route('customers.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 500; font-size: 0.9rem;">
        &rarr; عودة لقائمة العملاء
    </a>
</div>

<!-- Customer Profile Header -->
<div class="card" style="margin-bottom: 20px; border-top: 4px solid var(--primary);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="background: var(--primary); color: white; border-radius: 50%; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.6rem;">
                {{ mb_substr($customer->name, 0, 1) }}
            </div>
            <div>
                <h1 style="margin: 0; font-size: 1.5rem; color: var(--text-primary);">{{ $customer->name }}</h1>
                <div style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 4px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <span>📱 {{ $customer->phone_number }}</span>
                    @if($customer->whatsapp_number)
                        <span>💬 واتساب: {{ $customer->whatsapp_number }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div>
            @if($customer->hasLateInstallments)
                <span style="background-color: var(--danger); color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">
                    قسط متأخر ⚠️
                </span>
            @else
                <span style="background-color: #10B981; color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">
                    حساب منتظم ✅
                </span>
            @endif
        </div>
    </div>

    <!-- Quick Action Bar -->
    @php
        $phone = $customer->whatsapp_number ?: $customer->phone_number;
        $portalUrl = route('customer.portal', ['token' => $customer->access_token]);
        $waMessage = urlencode("مرحباً {$customer->name} 👋\nيمكنك متابعة كشف حسابك والأقساط الخاصة بك مباشرة عبر رابطك الخاص:\n" . $portalUrl);
    @endphp
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; border-top: 1px solid var(--border); padding-top: 14px;">
        @if($phone)
            <a href="https://wa.me/{{ $phone }}?text={{ $waMessage }}" target="_blank" class="btn btn-secondary" style="padding: 8px; font-size: 0.85rem; background: #25D366; color: white; border: none; text-align: center;">
                💬 مشاركة رابط البوابة عبر الواتساب
            </a>
        @endif
        <button type="button" onclick="navigator.clipboard.writeText('{{ $portalUrl }}'); alert('تم نسخ رابط بوابة العميل بنجاح!');" class="btn btn-secondary" style="padding: 8px; font-size: 0.85rem;">
            📋 نسخ رابط بوابة العميل
        </button>
    </div>
</div>

<!-- Financial Summary Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0; text-align: center; padding: 14px;">
        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px;">إجمالي العقود</div>
        <div style="font-size: 1.2rem; font-weight: bold; color: var(--primary);">{{ number_format($totalAmount, 2) }}</div>
        <div style="font-size: 0.75rem; color: var(--text-secondary);">ج.م</div>
    </div>
    <div class="card" style="margin-bottom: 0; text-align: center; padding: 14px; border-color: #84E1BC;">
        <div style="font-size: 0.8rem; color: #03543F; margin-bottom: 4px;">تم سداده</div>
        <div style="font-size: 1.2rem; font-weight: bold; color: #0E9F6E;">{{ number_format($totalPaid, 2) }}</div>
        <div style="font-size: 0.75rem; color: #0E9F6E;">ج.م</div>
    </div>
    <div class="card" style="margin-bottom: 0; text-align: center; padding: 14px; border-color: #F8B4B4; background: #FDF2F2;">
        <div style="font-size: 0.8rem; color: #9B1C1C; margin-bottom: 4px;">المتبقي عليه</div>
        <div style="font-size: 1.2rem; font-weight: bold; color: #E02424;">{{ number_format($remainingAmount, 2) }}</div>
        <div style="font-size: 0.75rem; color: #E02424;">ج.م</div>
    </div>
</div>

<!-- Detailed Contracts & Installments -->
<h2 style="margin-bottom: 16px; font-size: 1.2rem; color: var(--text-primary);">عقود التقسيط والتفاصيل الكاملة</h2>

@forelse($customer->installmentPlans as $planIndex => $plan)
    <div class="card" style="margin-bottom: 20px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 12px; margin-bottom: 16px;">
            <div>
                <h3 style="margin: 0; font-size: 1.1rem; color: var(--primary);">عقد تقسيط رقم #{{ $planIndex + 1 }}</h3>
                <span style="font-size: 0.8rem; color: var(--text-secondary);">تاريخ العقد: {{ $plan->created_at->format('Y-m-d') }}</span>
            </div>
            <div style="text-align: left;">
                <div style="font-size: 0.85rem; color: var(--text-secondary);">المتبقي في هذا العقد:</div>
                <div style="font-size: 1.1rem; font-weight: bold; color: var(--danger);">{{ number_format($plan->remaining_amount, 2) }} ج.م</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; background: var(--background); padding: 10px; border-radius: var(--radius-md); font-size: 0.85rem; margin-bottom: 16px;">
            <div><span>إجمالي العقد:</span> <strong>{{ number_format($plan->total_amount, 2) }} ج.م</strong></div>
            <div><span>الدفعة المقدمة:</span> <strong>{{ number_format($plan->down_payment, 2) }} ج.م</strong></div>
            <div><span>عدد الأقساط:</span> <strong>{{ $plan->installments->count() }} قسط</strong></div>
        </div>

        <h4 style="margin-bottom: 12px; font-size: 0.95rem; color: var(--text-secondary);">جدول الأقساط:</h4>
        
        <div style="display: flex; flex-direction: column; gap: 8px;">
            @foreach($plan->installments as $instIndex => $inst)
                @php
                    $isLate = !$inst->is_paid && $inst->due_date->isPast();
                    $pendingPostponement = $inst->latestPostponementRequest && $inst->latestPostponementRequest->status === 'pending';
                @endphp
                <div style="display: flex; justify-content: space-between; align-items: center; background: var(--surface); border: 1px solid var(--border); padding: 10px 14px; border-radius: var(--radius-sm); font-size: 0.875rem;">
                    <div>
                        <div style="font-weight: bold;">قسط {{ $instIndex + 1 }}: {{ number_format($inst->amount, 2) }} ج.م</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px;">
                            استحقاق: {{ $inst->due_date->format('Y-m-d') }}
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px;">
                        @if($inst->is_paid)
                            <span style="color: #059669; font-weight: bold; background: #DEF7EC; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem;">
                                تم السداد ✓ ({{ $inst->paid_date ? $inst->paid_date->format('Y-m-d') : '' }})
                            </span>
                        @else
                            @if($pendingPostponement)
                                <span style="background: #FEF3C7; color: #92400E; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">
                                    طلب تأجيل معلق ⏳
                                </span>
                            @elseif($isLate)
                                <span style="background: #FDE8E8; color: #9B1C1C; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">
                                    متأخر ⚠️
                                </span>
                            @endif

                            <form action="{{ route('installments.pay', $inst->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" style="background: var(--primary); color: white; border: none; padding: 6px 14px; border-radius: var(--radius-md); cursor: pointer; font-size: 0.8rem; font-weight: bold;">
                                    تسجيل السداد
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="card" style="text-align: center; color: var(--text-secondary); padding: 32px;">
        لا توجد عقود تقسيط مسجلة لهذا العميل حتى الآن.
    </div>
@endforelse
@endsection
