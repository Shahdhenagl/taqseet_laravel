<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_receives_notifications_at_exact_due_date_milestones()
    {
        $customer = Customer::create(['name' => 'خالد سعيد', 'phone_number' => '01055554444']);
        $plan = InstallmentPlan::create([
            'customer_id' => $customer->id,
            'total_amount' => 12000,
            'down_payment' => 2000,
            'remaining_amount' => 10000,
        ]);

        // Create installments for 30, 15, 7, 3, 1, 0 days
        $daysList = [30, 15, 7, 3, 1, 0];
        foreach ($daysList as $d) {
            Installment::create([
                'plan_id' => $plan->id,
                'amount' => 1000,
                'due_date' => now()->addDays($d),
                'is_paid' => false,
            ]);
        }

        $this->artisan('installments:notify-admin')
            ->expectsOutputToContain('Admin Notification Scan Completed')
            ->assertExitCode(0);

        $notificationsCount = DB::table('notifications')->count();
        $this->assertEquals(6, $notificationsCount);
    }

    public function test_can_fetch_admin_notifications_api()
    {
        $customer = Customer::create(['name' => 'خالد سعيد', 'phone_number' => '01055554444']);
        $plan = InstallmentPlan::create([
            'customer_id' => $customer->id,
            'total_amount' => 12000,
            'down_payment' => 2000,
            'remaining_amount' => 10000,
        ]);
        Installment::create([
            'plan_id' => $plan->id,
            'amount' => 1000,
            'due_date' => now()->addDays(3),
            'is_paid' => false,
        ]);

        $this->artisan('installments:notify-admin');

        $response = $this->getJson('/admin/api/notifications');
        $response->assertStatus(200);
        $response->assertJsonPath('unread_count', 1);
        $response->assertJsonPath('notifications.0.label', 'باقي 3 أيام');
    }
}
