<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة العميل - حساب الأقساط</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('taqseet_theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <style>
        .theme-toggle-btn {
            background: var(--surface, #FFFFFF);
            border: 1px solid var(--border, #E2E8F0);
            color: var(--text-primary, #0F172A);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            font-family: inherit;
        }
        .theme-toggle-btn:hover {
            border-color: var(--primary, #6366F1);
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        .badge-paid { background: #DEF7EC; color: #03543F; }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-late { background: #FDE8E8; color: #9B1C1C; }
        .badge-upcoming { background: #E1EFFE; color: #1E429F; }
        
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.6);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .modal-overlay.active { display: flex; }
        .modal-card {
            background: var(--surface, #FFFFFF);
            color: var(--text-primary, #0F172A);
            border: 1px solid var(--border, #E2E8F0);
            border-radius: var(--radius-lg, 20px);
            width: 100%;
            max-width: 480px;
            padding: 24px;
            box-shadow: var(--shadow-lg);
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 500px; padding-bottom: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; margin-bottom: 8px;">
            <div style="font-weight: bold; color: var(--primary); font-size: 1rem;">
                📱 بوابة العميل
            </div>
            <button type="button" id="portalThemeBtn" onclick="toggleThemePortal()" class="theme-toggle-btn">
                <span id="portalThemeIcon">🌙</span>
                <span id="portalThemeText">الوضع الداكن</span>
            </button>
        </div>

        <header style="margin-bottom: 20px; padding-top: 8px; text-align: center;">
            <div style="background: var(--primary); color: white; display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; font-size: 1.5rem; font-weight: bold; margin-bottom: 12px;">
                {{ mb_substr($customer->name, 0, 1) }}
            </div>
            <h2 style="color: var(--text-primary); margin-bottom: 4px;">أهلاً بك، {{ $customer->name }}</h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">ملخص خطة التقسيط والأقساط الخاصة بك</p>
        </header>

        @if(session('success'))
            <div class="card" style="background: #DEF7EC; color: #03543F; border-color: #84E1BC; padding: 12px 16px; margin-bottom: 16px; border-radius: var(--radius-md);">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="card" style="background: #FDE8E8; color: #9B1C1C; border-color: #F8B4B4; padding: 12px 16px; margin-bottom: 16px; border-radius: var(--radius-md);">
                {{ session('error') }}
            </div>
        @endif

        <!-- Summary Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 20px;">
            <div class="card" style="margin-bottom: 0; padding: 12px; text-align: center;">
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 4px;">إجمالي المبالغ</div>
                <div style="font-size: 0.95rem; font-weight: bold; color: var(--text-primary);">{{ number_format($totalAmount, 2) }}</div>
                <div style="font-size: 0.7rem; color: var(--text-secondary);">ج.م</div>
            </div>
            <div class="card" style="margin-bottom: 0; padding: 12px; text-align: center; border-color: #84E1BC;">
                <div style="font-size: 0.75rem; color: #03543F; margin-bottom: 4px;">تم سداده</div>
                <div style="font-size: 0.95rem; font-weight: bold; color: #0E9F6E;">{{ number_format($totalPaid, 2) }}</div>
                <div style="font-size: 0.7rem; color: #0E9F6E;">ج.م</div>
            </div>
            <div class="card" style="margin-bottom: 0; padding: 12px; text-align: center; border-color: #F8B4B4; background: rgba(253, 242, 242, 0.5);">
                <div style="font-size: 0.75rem; color: #9B1C1C; margin-bottom: 4px;">المتبقي عليك</div>
                <div style="font-size: 0.95rem; font-weight: bold; color: #E02424;">{{ number_format($remainingAmount, 2) }}</div>
                <div style="font-size: 0.7rem; color: #E02424;">ج.م</div>
            </div>
        </div>

        <!-- Installments List -->
        <h3 style="margin-bottom: 12px; font-size: 1.1rem; display: flex; justify-content: space-between; align-items: center; color: var(--text-primary);">
            <span>جدول الأقساط</span>
            <span style="font-size: 0.85rem; color: var(--text-secondary);">عدد الأقساط: {{ $allInstallments->count() }}</span>
        </h3>

        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($allInstallments as $index => $installment)
                @php
                    $isLate = !$installment->is_paid && $installment->due_date->isPast();
                    $pendingPostponement = $installment->latestPostponementRequest && $installment->latestPostponementRequest->status === 'pending';
                @endphp
                <div class="card" style="margin-bottom: 0; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <div>
                            <div style="font-weight: bold; font-size: 1rem; color: var(--text-primary);">قسط رقم #{{ $index + 1 }}</div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 2px;">
                                تاريخ الاستحقاق: {{ $installment->due_date->format('Y-m-d') }}
                            </div>
                        </div>
                        <div style="text-align: left;">
                            <div style="font-weight: bold; color: var(--primary); font-size: 1.1rem;">
                                {{ number_format($installment->amount, 2) }} ج.م
                            </div>
                            @if($installment->is_paid)
                                <span class="badge badge-paid">مسدد</span>
                            @elseif($pendingPostponement)
                                <span class="badge badge-pending">طلب تأجيل معلق</span>
                            @elseif($isLate)
                                <span class="badge badge-late">متأخر عن السداد</span>
                            @else
                                <span class="badge badge-upcoming">قادم</span>
                            @endif
                        </div>
                    </div>

                    @if(!$installment->is_paid)
                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed var(--border); display: flex; justify-content: space-between; align-items: center;">
                            @if($pendingPostponement)
                                <span style="font-size: 0.8rem; color: #92400E;">تم إرسال طلب تأجيل لتاريخ {{ $installment->latestPostponementRequest->requested_due_date->format('Y-m-d') }} وبانتظار الموافقة.</span>
                            @else
                                <button type="button" onclick="openPostponeModal('{{ $installment->id }}', '{{ $installment->due_date->format('Y-m-d') }}', '{{ number_format($installment->amount, 2) }}')" 
                                        style="background: var(--background); color: var(--primary); border: 1px solid var(--primary); border-radius: var(--radius-md); padding: 8px 14px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%;">
                                    طلب تأجيل القسط ⏱️
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="card" style="text-align: center; color: var(--text-secondary); padding: 24px;">
                    لا يوجد أقساط مسجلة لهذا الحساب حالياً.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Postponement Modal -->
    <div id="postponeModal" class="modal-overlay">
        <div class="modal-card">
            <h3 style="margin-bottom: 16px; color: var(--text-primary);">طلب تأجيل موعد القسط</h3>
            
            <form action="{{ route('customer.postpone', ['token' => $customer->access_token]) }}" method="POST">
                @csrf
                <input type="hidden" name="installment_id" id="modal_installment_id">
                
                <div class="input-group">
                    <label>تاريخ الاستحقاق الجديد المطلوب</label>
                    <input type="date" name="requested_due_date" id="modal_requested_date" class="input" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>

                <div class="input-group">
                    <label>سبب طلب التأجيل (اختياري)</label>
                    <textarea name="reason" class="input" rows="3" placeholder="اكتب سبب طلب التأجيل ليطلع عليه صاحب المحل..."></textarea>
                </div>

                <div style="background: #FEF3C7; color: #92400E; padding: 10px 12px; border-radius: var(--radius-md); font-size: 0.8rem; margin-bottom: 16px;">
                    💡 ملاحظة: عند موافقة صاحب المحل على طلب التأجيل قد تتم إضافة فائدة أو رسوم تأجيل بسيطة على قيمة القسط.
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">إرسال الطلب</button>
                    <button type="button" class="btn btn-secondary" onclick="closePostponeModal()" style="flex: 1;">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updatePortalToggleUI(theme) {
            const icon = document.getElementById('portalThemeIcon');
            const text = document.getElementById('portalThemeText');
            if (!icon || !text) return;
            if (theme === 'dark') {
                icon.innerText = '☀️';
                text.innerText = 'الوضع المضيء';
            } else {
                icon.innerText = '🌙';
                text.innerText = 'الوضع الداكن';
            }
        }

        function toggleThemePortal() {
            const current = document.documentElement.getAttribute('data-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('taqseet_theme', next);
            updatePortalToggleUI(next);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const activeTheme = document.documentElement.getAttribute('data-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            updatePortalToggleUI(activeTheme);
        });

        function openPostponeModal(installmentId, currentDueDate, amount) {
            document.getElementById('modal_installment_id').value = installmentId;
            let d = new Date(currentDueDate);
            d.setDate(d.getDate() + 30);
            let nextMonth = d.toISOString().split('T')[0];
            document.getElementById('modal_requested_date').value = nextMonth;
            document.getElementById('postponeModal').classList.add('active');
        }

        function closePostponeModal() {
            document.getElementById('postponeModal').classList.remove('active');
        }
    </script>
</body>
</html>
