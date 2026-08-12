@extends('layouts.app')

@section('content')
<header style="margin-bottom: 24px; padding-top: 20px;">
    <h1 style="color: var(--primary); margin-bottom: 8px;">إدارة طلبات تأجيل الأقساط</h1>
    <p style="color: var(--text-secondary);">مراجعة طلبات التأجيل المقدمة من العملاء والتحكم في الفوائد الإضافية</p>
</header>

<!-- Filter / Pending Counter -->
<div class="card" style="background: linear-gradient(135deg, #F59E0B, #D97706); color: white; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h3 style="margin: 0;">طلبات معلقة تنتظر قرارك</h3>
        <p style="margin-top: 4px; font-size: 0.875rem; opacity: 0.9;">طلبات التأجيل الجديدة المقترحة من العملاء</p>
    </div>
    <div style="font-size: 2.2rem; font-weight: bold; background: rgba(255,255,255,0.2); padding: 4px 18px; border-radius: var(--radius-md);">
        {{ $pendingCount }}
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 16px;">
    @forelse($requests as $req)
        <div class="card" style="margin-bottom: 0; border-right: 6px solid {{ $req->status === 'pending' ? '#F59E0B' : ($req->status === 'approved' ? '#10B981' : '#EF4444') }};">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem; color: var(--text-primary);">{{ $req->customer->name }}</h3>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 2px;">
                        رقم الهاتف: {{ $req->customer->phone_number }}
                    </div>
                </div>
                <div>
                    @if($req->status === 'pending')
                        <span style="background: #FEF3C7; color: #92400E; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">معلق ⏳</span>
                    @elseif($req->status === 'approved')
                        <span style="background: #DEF7EC; color: #03543F; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">مقبول ✅</span>
                    @else
                        <span style="background: #FDE8E8; color: #9B1C1C; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">مرفوض ❌</span>
                    @endif
                </div>
            </div>

            <div style="background: var(--background); padding: 12px; border-radius: var(--radius-md); font-size: 0.9rem; margin-bottom: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <div>
                    <span style="color: var(--text-secondary);">قيمة القسط الأصلية:</span>
                    <strong>{{ number_format($req->installment->amount, 2) }} ج.م</strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary);">تاريخ الاستحقاق الحالي:</span>
                    <strong>{{ $req->installment->due_date->format('Y-m-d') }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary);">التاريخ المطلوبة للتأجيل:</span>
                    <strong style="color: #D97706;">{{ $req->requested_due_date->format('Y-m-d') }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary);">سبب التأجيل:</span>
                    <span>{{ $req->reason ?? 'لم يذكر' }}</span>
                </div>
            </div>

            @if($req->status === 'approved')
                <div style="font-size: 0.85rem; color: #047857; background: #ECFDF5; padding: 8px 12px; border-radius: var(--radius-md);">
                    تمت الموافقة وتحديث موعد القسط. الفائدة الإضافية المضافة: <strong>+{{ number_format($req->extra_interest, 2) }} ج.م</strong>
                </div>
            @elseif($req->status === 'rejected')
                <div style="font-size: 0.85rem; color: #B91C1C; background: #FEF2F2; padding: 8px 12px; border-radius: var(--radius-md);">
                    تم رفض طلب التأجيل من صاحب المحل.
                </div>
            @else
                <div style="display: flex; gap: 8px; margin-top: 8px;">
                    <button type="button" class="btn btn-primary" style="flex: 1; background: #10B981;" 
                            onclick="openApproveModal('{{ $req->id }}', '{{ addslashes($req->customer->name) }}', {{ $req->installment->amount }}, '{{ $req->requested_due_date->format('Y-m-d') }}')">
                        موافقة وإضافة فائدة ✅
                    </button>
                    <form action="{{ route('admin.postponements.reject', ['id' => $req->id]) }}" method="POST" style="flex: 1;">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="border-color: #EF4444; color: #EF4444; width: 100%;">
                            رفض الطلب ❌
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="card" style="text-align: center; color: var(--text-secondary); padding: 32px;">
            لا توجد طلبات تأجيل أقساط حالياً.
        </div>
    @endforelse
</div>

<!-- Approve & Set Interest Modal -->
<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: var(--surface); border-radius: var(--radius-lg); width: 100%; max-width: 480px; padding: 24px; box-shadow: var(--shadow-lg);">
        <h3 style="margin-bottom: 12px; color: var(--text-primary);" id="approveModalTitle">الموافقة على التأجيل</h3>
        
        <form id="approveForm" method="POST">
            @csrf
            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 16px;">
                سيتم ترحيل تاريخ استحقاق القسط إلى: <strong id="modalNewDate" style="color: var(--primary);"></strong>
            </div>

            <div class="input-group">
                <label>الفائدة / الرسوم الإضافية للتأجيل (ج.م)</label>
                <input type="number" step="0.01" name="extra_interest" id="extra_interest_input" class="input" value="0" min="0" oninput="calculateNewTotal()">
                <span style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 4px; display: block;">أدخل 0 إذا كنت لا ترغب بإضافة فائدة على التأجيل.</span>
            </div>

            <div style="background: var(--background); padding: 12px; border-radius: var(--radius-md); margin-bottom: 16px; font-size: 0.9rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span>قيمة القسط السابقة:</span>
                    <span id="oldAmountSpan">0.00 ج.م</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: bold; color: var(--primary); font-size: 1rem; border-top: 1px solid var(--border); padding-top: 4px; margin-top: 4px;">
                    <span>قيمة القسط الجديدة:</span>
                    <span id="newAmountSpan">0.00 ج.م</span>
                </div>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="flex: 1; background: #10B981;">تأكيد الموافقة</button>
                <button type="button" class="btn btn-secondary" onclick="closeApproveModal()" style="flex: 1;">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentInstallmentAmount = 0;

    function openApproveModal(requestId, customerName, currentAmount, newDate) {
        currentInstallmentAmount = currentAmount;
        document.getElementById('approveForm').action = '/admin/postponements/' + requestId + '/approve';
        document.getElementById('approveModalTitle').innerText = 'الموافقة على تأجيل قسط ' + customerName;
        document.getElementById('modalNewDate').innerText = newDate;
        document.getElementById('oldAmountSpan').innerText = currentAmount.toFixed(2) + ' ج.م';
        document.getElementById('extra_interest_input').value = 0;
        calculateNewTotal();

        document.getElementById('approveModal').style.display = 'flex';
    }

    function closeApproveModal() {
        document.getElementById('approveModal').style.display = 'none';
    }

    function calculateNewTotal() {
        let extra = parseFloat(document.getElementById('extra_interest_input').value) || 0;
        let newAmount = currentInstallmentAmount + extra;
        document.getElementById('newAmountSpan').innerText = newAmount.toFixed(2) + ' ج.م';
    }
</script>
@endsection
