@extends('layouts.app')

@section('content')
<header style="margin-bottom: 24px; padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h1 style="color: var(--primary); margin: 0;">العملاء والأقساط</h1>
    <button onclick="document.getElementById('customerForm').style.display='block'" class="btn btn-primary" style="width: auto; padding: 8px 16px;">+ عميل جديد</button>
</header>

<!-- Add Customer Form -->
<div id="customerForm" class="card" style="display: none; border: 2px solid var(--primary); margin-bottom: 24px;">
    <h3 style="margin-bottom: 16px;">إضافة عميل جديد</h3>
    <form action="{{ route('customers.store') }}" method="POST">
        @csrf
        <div class="input-group">
            <label>اسم العميل الكامل</label>
            <input type="text" name="name" class="input" required placeholder="أحمد محمود">
        </div>
        <div class="input-group">
            <label>رقم الهاتف</label>
            <input type="text" name="phone_number" class="input" required placeholder="01012345678">
        </div>
        <div class="input-group">
            <label>رقم الواتساب (مع كود الدولة)</label>
            <input type="text" name="whatsapp_number" class="input" placeholder="201012345678">
        </div>
        <div style="display: flex; gap: 12px; margin-top: 12px;">
            <button type="submit" class="btn btn-primary">حفظ العميل</button>
            <button type="button" onclick="document.getElementById('customerForm').style.display='none'" class="btn btn-secondary">إلغاء</button>
        </div>
    </form>
</div>

<div style="display: flex; flex-direction: column; gap: 16px;">
    @foreach($customers as $customer)
        <div class="card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <div>
                    <h3 style="margin: 0; margin-bottom: 4px;">{{ $customer->name }}</h3>
                    <p style="font-size: 0.875rem; color: var(--text-secondary);">📱 {{ $customer->phone_number }}</p>
                </div>
                
                <div>
                    @if($customer->hasLateInstallments)
                        <span style="background-color: var(--danger); color: white; padding: 4px 10px; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: bold;">
                            قسط متأخر ⚠️
                        </span>
                    @else
                        <span style="background-color: var(--secondary); color: white; padding: 4px 10px; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: bold;">
                            منتظم ✅
                        </span>
                    @endif
                </div>
            </div>

            <!-- Installment Plans Details -->
            @foreach($customer->installmentPlans as $plan)
                <div style="background: var(--background); padding: 12px; border-radius: var(--radius-md); margin-top: 8px;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 8px; font-weight: bold;">
                        <span>إجمالي العقد: {{ number_format($plan->total_amount, 2) }} ج.م</span>
                        <span>المتبقي: {{ number_format($plan->remaining_amount, 2) }} ج.م</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        @foreach($plan->installments as $inst)
                            <div style="display: flex; justify-content: space-between; align-items: center; background: var(--surface); padding: 8px 12px; border-radius: var(--radius-sm); font-size: 0.8rem;">
                                <div>
                                    <span>مبلغ القسط: {{ number_format($inst->amount, 2) }} ج.م</span> | 
                                    <span style="color: var(--text-secondary);">استحقاق: {{ $inst->due_date->format('Y-m-d') }}</span>
                                </div>
                                <div>
                                    @if($inst->is_paid)
                                        <span style="color: var(--secondary); font-weight: bold;">تم السداد ✓</span>
                                    @else
                                        <form action="{{ route('installments.pay', $inst->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" style="background: var(--primary); color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">سداد القسط</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- WhatsApp & Customer Portal Link -->
            @php
                $phone = $customer->whatsapp_number ?: $customer->phone_number;
                $portalUrl = route('customer.portal', ['token' => $customer->access_token]);
                $waMessage = urlencode("مرحباً {$customer->name} 👋\nيمكنك متابعة جدول أقساطك والمتبقي عليك وتقديم طلبات التأجيل مباشرة عبر هذا الرابط الخاص بك:\n" . $portalUrl);
            @endphp
            <div style="margin-top: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                @if($phone)
                    <a href="https://wa.me/{{ $phone }}?text={{ $waMessage }}" target="_blank" class="btn btn-secondary" style="padding: 8px 12px; font-size: 0.8rem; background: #25D366; color: white; border: none;">
                        💬 إرسال رابط البوابة بالواتساب
                    </a>
                @endif
                <button type="button" onclick="navigator.clipboard.writeText('{{ $portalUrl }}'); alert('تم نسخ رابط بوابة العميل بنجاح!');" class="btn btn-secondary" style="padding: 8px 12px; font-size: 0.8rem;">
                    📋 نسخ رابط البوابة
                </button>
            </div>
        </div>
    @endforeach
</div>
@endsection
