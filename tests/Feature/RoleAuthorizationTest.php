<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_owner_cannot_access_admin_dashboard(): void
    {
        $owner = \App\Models\User::factory()->create(['role' => 'owner']);
        $response = $this->actingAs($owner)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = \App\Models\User::factory()->create(['role' => 'customer']);
        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_owner_can_access_owner_dashboard(): void
    {
        $owner = \App\Models\User::factory()->create(['role' => 'owner']);
        $response = $this->actingAs($owner)->get('/owner/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_owner_dashboard(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/owner/dashboard');
        $response->assertStatus(403);
    }

    public function test_customer_cannot_access_owner_dashboard(): void
    {
        $customer = \App\Models\User::factory()->create(['role' => 'customer']);
        $response = $this->actingAs($customer)->get('/owner/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_is_redirected_to_admin_dashboard_on_login(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_owner_is_redirected_to_owner_dashboard_on_login(): void
    {
        $owner = \App\Models\User::factory()->create(['role' => 'owner']);
        $response = $this->actingAs($owner)->get('/dashboard');
        $response->assertRedirect('/owner/dashboard');
    }

    public function test_customer_redirected_to_customer_dashboard_on_login(): void
    {
        $customer = \App\Models\User::factory()->create(['role' => 'customer']);
        $response = $this->actingAs($customer)->get('/dashboard');
        $response->assertRedirect('/customer/dashboard'); 
    }

    public function test_unauthenticated_user_is_redirected_to_login_from_protected_routes(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
