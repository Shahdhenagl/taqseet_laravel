<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractPdfAndCronTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_contract_pdf()
    {
        $customer = Customer::create(['name' => 'سامي أحمد', 'phone_number' => '01011112222']);
        $plan = InstallmentPlan::create([
            'customer_id' => $customer->id,
            'total_amount' => 10000,
            'down_payment' => 2000,
            'remaining_amount' => 8000,
        ]);

        $response = $this->get('/plans/' . $plan->id . '/contract-pdf?html=1');
        $response->assertStatus(200);
        $response->assertSee('عقد بيع بالتقسيط وإقرار دين');
        $response->assertSee('سامي أحمد');
    }

    public function test_can_view_receipt_pdf()
    {
        $customer = Customer::create(['name' => 'سامي أحمد', 'phone_number' => '01011112222']);
        $plan = InstallmentPlan::create([
            'customer_id' => $customer->id,
            'total_amount' => 10000,
            'down_payment' => 2000,
            'remaining_amount' => 8000,
        ]);
        $installment = Installment::create([
            'plan_id' => $plan->id,
            'amount' => 2000,
            'due_date' => now(),
            'is_paid' => true,
            'paid_date' => now(),
        ]);

        $response = $this->get('/installments/' . $installment->id . '/receipt-pdf?html=1');
        $response->assertStatus(200);
        $response->assertSee('إيصال استلام نقدية');
        $response->assertSee('سامي أحمد');
    }

    public function test_whatsapp_reminder_cron_command()
    {
        $customer = Customer::create(['name' => 'سامي أحمد', 'phone_number' => '01011112222']);
        $plan = InstallmentPlan::create([
            'customer_id' => $customer->id,
            'total_amount' => 10000,
            'down_payment' => 2000,
            'remaining_amount' => 8000,
        ]);
        Installment::create([
            'plan_id' => $plan->id,
            'amount' => 2000,
            'due_date' => now()->addDays(2),
            'is_paid' => false,
        ]);

        $this->artisan('installments:remind')
            ->expectsOutputToContain('Found 1 upcoming installments')
            ->assertExitCode(0);
    }
}
