<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create sample products
        $p1 = Product::create([
            'name' => 'ثلاجة توشيبا 14 قدم نVolume',
            'description' => 'ثلاجة توشيبا نوفروست 14 قدم شاشة ديجيتال',
            'price' => 18500.00,
            'stock' => 5,
        ]);

        $p2 = Product::create([
            'name' => 'غسالة زانوسي 7 كيلو',
            'description' => 'غسالة فول أوتوماتيك تحميل أمامي',
            'price' => 14200.00,
            'stock' => 3,
        ]);

        $p3 = Product::create([
            'name' => 'شاشة سامسونج 55 بوصة 4K',
            'description' => 'شاشة سمارت 4K Ultra HD',
            'price' => 16800.00,
            'stock' => 8,
        ]);

        // 2. Create sample customer
        $customer = Customer::create([
            'name' => 'أحمد فتحي عبد السلام',
            'phone_number' => '01098765432',
            'whatsapp_number' => '201098765432',
        ]);

        // 3. Create invoice and installment plan for customer
        $invoice = Invoice::create([
            'total_amount' => 18500.00,
            'is_paid' => false,
            'customer_id' => $customer->id,
        ]);

        $invoice->items()->create([
            'product_id' => $p1->id,
            'quantity' => 1,
            'price' => 18500.00,
        ]);

        $downPayment = 3500.00;
        $remaining = 15000.00;
        $months = 6;
        $monthlyAmount = 2500.00;

        $plan = InstallmentPlan::create([
            'total_amount' => 18500.00,
            'down_payment' => $downPayment,
            'remaining_amount' => $remaining,
            'customer_id' => $customer->id,
        ]);

        // Create 6 monthly installments (1 paid, 1 overdue, 4 upcoming)
        for ($i = 1; $i <= $months; $i++) {
            $dueDate = now()->addMonths($i - 2); // First month was last month
            $isPaid = ($i === 1);
            
            Installment::create([
                'plan_id' => $plan->id,
                'amount' => $monthlyAmount,
                'due_date' => $dueDate,
                'is_paid' => $isPaid,
                'paid_date' => $isPaid ? now()->subDays(15) : null,
            ]);
        }
    }
}
