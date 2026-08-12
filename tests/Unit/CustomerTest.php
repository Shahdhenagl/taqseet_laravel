<?php

namespace Tests\Unit;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_generates_access_token_on_creation()
    {
        $customer = Customer::create([
            'name' => 'عميل وحدة',
            'phone_number' => '01055556666',
        ]);

        $this->assertNotEmpty($customer->access_token);
        $this->assertEquals(32, strlen($customer->access_token));
    }
}
