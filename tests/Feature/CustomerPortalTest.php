<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_their_portal_with_valid_token()
    {
        $customer = Customer::create([
            'name' => 'اختبار العميل',
            'phone_number' => '01011112222',
        ]);

        $response = $this->get('/c/' . $customer->access_token);

        $response->assertStatus(200);
        $response->assertSee('اختبار العميل');
    }

    public function test_invalid_token_returns_404()
    {
        $response = $this->get('/c/invalid-token-123');

        $response->assertStatus(404);
    }

    public function test_customer_can_submit_postponement_request()
    {
        $customer = Customer::create([
            'name' => 'عميل تأجيل',
            'phone_number' => '01022223333',
        ]);

        $plan = InstallmentPlan::create([
            'total_amount' => 1000,
            'down_payment' => 200,
            'remaining_amount' => 800,
            'customer_id' => $customer->id,
        ]);

        $installment = Installment::create([
            'plan_id' => $plan->id,
            'amount' => 400,
            'due_date' => now()->addDays(5),
            'is_paid' => false,
        ]);

        $requestedDate = now()->addDays(30)->format('Y-m-d');

        $response = $this->post('/c/' . $customer->access_token . '/postpone', [
            'installment_id' => $installment->id,
            'requested_due_date' => $requestedDate,
            'reason' => 'ظروف سفر',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('postponement_requests', [
            'customer_id' => $customer->id,
            'installment_id' => $installment->id,
            'status' => 'pending',
        ]);
    }
}
