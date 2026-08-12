<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إيصال سداد قسط - {{ $installment->plan->customer->name }}</title>
    <style>
        body {
            font-family: 'Tajawal', sans-serif, Arial;
            direction: rtl;
            text-align: right;
            padding: 16px;
            color: #1E293B;
            background: #fff;
        }
        .receipt-card {
            border: 2px solid #10B981;
            border-radius: 12px;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #10B981;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header h2 {
            color: #059669;
            margin: 0;
            font-size: 18pt;
        }
        .amount-box {
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            color: #047857;
            margin: 16px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11pt;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px; text-align: left;">
        <button onclick="window.print()" style="background: #10B981; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold;">
            🖨️ طباعة الإيصال
        </button>
    </div>

    <div class="receipt-card">
        <div class="header">
            <h2>إيصال استلام نقدية (سداد قسط)</h2>
            <p style="margin: 4px 0 0 0; color: #64748B; font-size: 9pt;">رقم الإيصال: #REC-{{ mb_substr($installment->id, 0, 8) }}</p>
        </div>

        <div class="row">
            <span>استلمنا من السيد / السيدة:</span>
            <strong>{{ $installment->plan->customer->name }}</strong>
        </div>
        <div class="row">
            <span>رقم الهاتف:</span>
            <span>{{ $installment->plan->customer->phone_number }}</span>
        </div>

        <div class="amount-box">
            المبلغ المدفوع: {{ number_format($installment->amount, 2) }} جنيه مصري
        </div>

        <div class="row">
            <span>تاريخ السداد:</span>
            <strong>{{ $installment->paid_date ? $installment->paid_date->format('Y-m-d H:i') : now()->format('Y-m-d') }}</strong>
        </div>
        <div class="row">
            <span>تاريخ استحقاق القسط الأصلي:</span>
            <span>{{ $installment->due_date->format('Y-m-d') }}</span>
        </div>
        <div class="row" style="border-top: 1px solid #E2E8F0; padding-top: 8px; margin-top: 12px;">
            <span>المتبقي في عقد التقسيط:</span>
            <strong style="color: #DC2626;">{{ number_format($installment->plan->remaining_amount, 2) }} ج.م</strong>
        </div>

        <div style="margin-top: 30px; text-align: left; font-size: 10pt;">
            <div>توقيع المستلم: ________________________</div>
        </div>
    </div>
</body>
</html>
