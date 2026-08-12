<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عقد بيع بالتقسيط - {{ $plan->customer->name }}</title>
    <style>
        body {
            font-family: 'Tajawal', sans-serif, Arial;
            direction: rtl;
            text-align: right;
            padding: 20px;
            color: #1E293B;
            line-height: 1.5;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4F46E5;
            margin: 0;
            font-size: 20pt;
        }
        .header p {
            color: #64748B;
            margin: 4px 0 0 0;
            font-size: 10pt;
        }
        .box {
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
            background: #F8FAFC;
        }
        .box-title {
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 8px;
            font-size: 11pt;
        }
        .grid {
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 10pt;
        }
        table, th, td {
            border: 1px solid #CBD5E1;
        }
        th {
            background-color: #EEF2FF;
            color: #3730A3;
            padding: 8px;
            text-align: center;
        }
        td {
            padding: 8px;
            text-align: center;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .sig-block {
            width: 45%;
            border-top: 1px solid #000;
            padding-top: 8px;
            font-weight: bold;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px; text-align: left;">
        <button onclick="window.print()" style="background: #4F46E5; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold;">
            🖨️ طباعة العقد
        </button>
    </div>

    <div class="header">
        <h1>عقد بيع بالتقسيط وإقرار دين</h1>
        <p>رقم العقد: #{{ mb_substr($plan->id, 0, 8) }} | تاريخ التحرير: {{ $plan->created_at->format('Y-m-d') }}</p>
    </div>

    <div class="box">
        <div class="box-title">الطرف الأول (البائع / المتجر):</div>
        <div><strong>نظام تقسيط وكاشير (Taqseet System)</strong> - هاتف: 01000000000</div>
    </div>

    <div class="box">
        <div class="box-title">الطرف الثاني (المشتري / العميل):</div>
        <div>الاسم الكامل: <strong>{{ $plan->customer->name }}</strong></div>
        <div>رقم الهاتف: <strong>{{ $plan->customer->phone_number }}</strong></div>
    </div>

    <div class="box">
        <div class="box-title">تفاصيل العقد المالي:</div>
        <table style="margin-top: 4px;">
            <tr>
                <th>إجمالي قيمة العقد</th>
                <th>المقدم المدفوع</th>
                <th>المبلغ المتبقي بالتقسيط</th>
                <th>عدد الأقساط</th>
            </tr>
            <tr>
                <td><strong>{{ number_format($plan->total_amount, 2) }} ج.م</strong></td>
                <td>{{ number_format($plan->down_payment, 2) }} ج.م</td>
                <td style="color: #DC2626;"><strong>{{ number_format($plan->remaining_amount, 2) }} ج.م</strong></td>
                <td>{{ $plan->installments->count() }} قسط</td>
            </tr>
        </table>
    </div>

    <div class="box-title" style="margin-top: 16px;">جدول استحقاق الأقساط الشهري:</div>
    <table>
        <thead>
            <tr>
                <th>رقم القسط</th>
                <th>تاريخ الاستحقاق</th>
                <th>قيمة القسط</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plan->installments as $index => $inst)
                <tr>
                    <td>قسط #{{ $index + 1 }}</td>
                    <td>{{ $inst->due_date->format('Y-m-d') }}</td>
                    <td><strong>{{ number_format($inst->amount, 2) }} ج.م</strong></td>
                    <td>{{ $inst->is_paid ? 'مسدد ✓' : 'غير مسدد' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 9pt; color: #475569;">
        <strong>إقرار واستلام:</strong> يقر الطرف الثاني بأنه تسلم السلعة المباعة بحالة جيدة ويلتزم بسداد الأقساط الموضحة أعلاه في مواعيد استحقاقها المحددة.
    </div>

    <table style="width: 100%; border: none; margin-top: 40px;">
        <tr style="border: none;">
            <td style="border: none; text-align: center; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 40px;">توقيع الطرف الأول (البائع)</div>
                ________________________
            </td>
            <td style="border: none; text-align: center; width: 50%;">
                <div style="font-weight: bold; margin-bottom: 40px;">توقيع الطرف الثاني (المشتري)</div>
                ________________________
            </td>
        </tr>
    </table>
</body>
</html>
