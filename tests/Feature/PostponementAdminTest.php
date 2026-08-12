<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\PostponementRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostponementAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_postponement_requests()
    {
        $response = $this->get('/admin/postponements');

        $response->assertStatus(200);
        $response->assertSee('إدارة طلبات تأجيل الأقساط');
    }

    public function test_admin_can_approve_postponement_request_with_extra_interest()
    {
        $customer = Customer::create([
            'name' => 'عميل موافقة',
            'phone_number' => '01033334444',
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

        $postponement = PostponementRequest::create([
            'customer_id' => $customer->id,
            'installment_id' => $installment->id,
            'requested_due_date' => $requestedDate,
            'reason' => 'طلب تأجيل',
            'status' => 'pending',
        ]);

        $response = $this->post('/admin/postponements/' . $postponement->id . '/approve', [
            'extra_interest' => 50,
        ]);

        $response->assertRedirect();

        // Verify request status is approved
        $this->assertDatabaseHas('postponement_requests', [
            'id' => $postponement->id,
            'status' => 'approved',
            'extra_interest' => 50,
        ]);

        // Verify installment amount increased from 400 to 450
        $installment->refresh();
        $this->assertEquals(450, $installment->amount);
        $this->assertEquals($requestedDate, $installment->due_date->format('Y-m-d'));

        // Verify plan amounts increased by 50
        $plan->refresh();
        $this->assertEquals(1050, $plan->total_amount);
        $this->assertEquals(850, $plan->remaining_amount);
    }

    public function test_admin_can_reject_postponement_request()
    {
        $customer = Customer::create([
            'name' => 'عميل رفض',
            'phone_number' => '01044445555',
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

        $postponement = PostponementRequest::create([
            'customer_id' => $customer->id,
            'installment_id' => $installment->id,
            'requested_due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $response = $this->post('/admin/postponements/' . $postponement->id . '/reject');

        $response->assertRedirect();
        $this->assertDatabaseHas('postponement_requests', [
            'id' => $postponement->id,
            'status' => 'rejected',
        ]);
    }
}
