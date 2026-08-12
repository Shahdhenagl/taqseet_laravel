<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerProfileAndSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_customer_profile_page()
    {
        $customer = Customer::create([
            'name' => 'محمد كمال',
            'phone_number' => '01099998888',
        ]);

        $response = $this->get('/customers/' . $customer->id);

        $response->assertStatus(200);
        $response->assertSee('محمد كمال');
        $response->assertSee('عقود التقسيط والتفاصيل الكاملة');
    }

    public function test_admin_can_search_customers()
    {
        Customer::create(['name' => 'مصطفى أحمد', 'phone_number' => '01111111111']);
        Customer::create(['name' => 'سارة علي', 'phone_number' => '01222222222']);

        $response = $this->get('/customers?search=مصطفى');

        $response->assertStatus(200);
        $response->assertSee('مصطفى أحمد');
        $response->assertDontSee('سارة علي');
    }

    public function test_admin_can_search_products()
    {
        Product::create(['name' => 'ثلاجة توشيبا', 'price' => 15000, 'stock' => 5]);
        Product::create(['name' => 'غسالة سامسونج', 'price' => 12000, 'stock' => 3]);

        $response = $this->get('/products?search=ثلاجة');

        $response->assertStatus(200);
        $response->assertSee('ثلاجة توشيبا');
        $response->assertDontSee('غسالة سامسونج');
    }
}
