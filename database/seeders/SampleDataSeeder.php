<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\Product;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Sample Products (Motorcycles / Motorcycles parts)
        $p1 = Product::create([
            'name' => 'دراجة نارية هوندا 150cc',
            'description' => 'دراجة نارية هوندا موديل 2024 بحالة الزيرو',
            'price' => 35000,
            'stock' => 5,
        ]);

        $p2 = Product::create([
            'name' => 'دراجة نارية ياماها 200cc',
            'description' => 'دراجة سباق سوداء 200 سي سي',
            'price' => 45000,
            'stock' => 2,
        ]);

        $p3 = Product::create([
            'name' => 'دراجة نارية سوزوكي 125cc',
            'description' => 'دراجة موفرة للبنزين 125 سي سي',
            'price' => 28000,
            'stock' => 10,
        ]);

        // Create Sample Customers
        $c1 = Customer::create([
            'name' => 'أحمد محمود',
            'phone_number' => '01012345678',
            'whatsapp_number' => '201012345678',
        ]);

        $c2 = Customer::create([
            'name' => 'محمد علي',
            'phone_number' => '01123456789',
            'whatsapp_number' => '201123456789',
        ]);

        $c3 = Customer::create([
            'name' => 'محمود سيد',
            'phone_number' => '01234567890',
            'whatsapp_number' => '201234567890',
        ]);

        // Create Installment Plan for Ahmed Mahmoud
        $plan = InstallmentPlan::create([
            'total_amount' => 35000,
            'down_payment' => 5000,
            'remaining_amount' => 30000,
            'customer_id' => $c1->id,
        ]);

        // Create 6 monthly installments (some paid, some late)
        Installment::create([
            'plan_id' => $plan->id,
            'amount' => 5000,
            'due_date' => now()->subMonths(2),
            'is_paid' => true,
            'paid_date' => now()->subMonths(2),
        ]);

        Installment::create([
            'plan_id' => $plan->id,
            'amount' => 5000,
            'due_date' => now()->subMonth(),
            'is_paid' => false, // Overdue!
        ]);

        Installment::create([
            'plan_id' => $plan->id,
            'amount' => 5000,
            'due_date' => now()->addDays(5),
            'is_paid' => false,
        ]);

        for ($i = 2; $i <= 4; $i++) {
            Installment::create([
                'plan_id' => $plan->id,
                'amount' => 5000,
                'due_date' => now()->addMonths($i),
                'is_paid' => false,
            ]);
        }
    }
}
