@extends('layouts.app')

@section('content')
<header style="margin-bottom: 20px; padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="color: var(--primary); margin: 0; margin-bottom: 4px;">دليل العملاء والتقسيط</h1>
        <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">إدارة العملاء واستعراض ملفاتهم الشخصية</p>
    </div>
    <button onclick="document.getElementById('customerForm').style.display='block'" class="btn btn-primary" style="width: auto; padding: 10px 18px;">+ عميل جديد</button>
</header>

<!-- Search Bar -->
<div class="card" style="margin-bottom: 20px; padding: 12px 16px;">
    <form action="{{ route('customers.index') }}" method="GET" style="display: flex; gap: 8px;">
        <div style="position: relative; flex: 1;">
            <input type="text" id="customerSearchInput" name="search" class="input" value="{{ request('search') }}" 
                   placeholder="🔍 ابحث عن عميل بالاسم أو رقم الهاتف..." onkeyup="filterCustomersLocal()">
        </div>
        @if(request('search'))
            <a href="{{ route('customers.index') }}" class="btn btn-secondary" style="width: auto; padding: 10px 16px;">إلغاء البحث</a>
        @endif
    </form>
</div>

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

<div id="customersContainer" style="display: flex; flex-direction: column; gap: 16px;">
    @forelse($customers as $customer)
        <div class="card customer-card" data-name="{{ strtolower($customer->name) }}" data-phone="{{ $customer->phone_number }}" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: var(--primary); color: white; border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem;">
                        {{ mb_substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 style="margin: 0; margin-bottom: 2px; font-size: 1.1rem;">
                            <a href="{{ route('customers.show', $customer->id) }}" style="color: var(--text-primary); text-decoration: none;">{{ $customer->name }}</a>
                        </h3>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0;">📱 {{ $customer->phone_number }}</p>
                    </div>
                </div>
                
                <div>
                    @if($customer->hasLateInstallments)
                        <span style="background-color: var(--danger); color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold;">
                            قسط متأخر ⚠️
                        </span>
                    @else
                        <span style="background-color: #10B981; color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold;">
                            منتظم ✅
                        </span>
                    @endif
                </div>
            </div>

            <!-- Summary Stats -->
            <div style="background: var(--background); padding: 10px 14px; border-radius: var(--radius-md); font-size: 0.875rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div>
                    <span style="color: var(--text-secondary);">عدد العقود:</span>
                    <strong>{{ $customer->installmentPlans->count() }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary);">المتبقي سداده:</span>
                    <strong style="color: var(--primary); font-size: 1rem;">{{ number_format($customer->remainingBalance, 2) }} ج.م</strong>
                </div>
            </div>

            <!-- Profile & WhatsApp Buttons -->
            @php
                $phone = $customer->whatsapp_number ?: $customer->phone_number;
                $token = $customer->access_token ?: ('token_' . substr(md5($customer->id), 0, 16));
                $portalUrl = route('customer.portal', ['token' => $token]);
                $waMessage = urlencode("مرحباً {$customer->name} 👋\nيمكنك متابعة جدول أقساطك والمتبقي عليك وتقديم طلبات التأجيل مباشرة عبر رابطك الخاص:\n" . $portalUrl);
            @endphp
            
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-primary" style="padding: 10px; font-size: 0.9rem;">
                    عرض البروفايل وتفاصيل الأقساط 👤
                </a>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    @if($phone)
                        <a href="https://wa.me/{{ $phone }}?text={{ $waMessage }}" target="_blank" class="btn btn-secondary" style="padding: 8px; font-size: 0.8rem; background: #25D366; color: white; border: none;">
                            💬 إرسال رابط البوابة بالواتساب
                        </a>
                    @endif
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $portalUrl }}'); alert('تم نسخ رابط بوابة العميل بنجاح!');" class="btn btn-secondary" style="padding: 8px; font-size: 0.8rem;">
                        📋 نسخ رابط البوابة
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="text-align: center; color: var(--text-secondary); padding: 32px;">
            لم يتم العثور على أي عملاء مطابقين للبحث.
        </div>
    @endforelse
</div>

<script>
    function filterCustomersLocal() {
        const input = document.getElementById('customerSearchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.customer-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const phone = card.getAttribute('data-phone');
            if (name.includes(input) || phone.includes(input)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
