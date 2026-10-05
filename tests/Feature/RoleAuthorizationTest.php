<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_owner_cannot_access_admin_dashboard(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $response = $this->actingAs($owner)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_owner_can_access_owner_dashboard(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->get('/owner/dashboard');
        $response->assertStatus(200);
    }

    public function test_owner_without_cafe_is_redirected_to_onboarding(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->get('/owner/dashboard');
        $response->assertRedirect(route('owner.cafe.create'));
    }

    public function test_admin_cannot_access_owner_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/owner/dashboard');
        $response->assertStatus(403);
    }

    public function test_customer_cannot_access_owner_dashboard(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $response = $this->actingAs($customer)->get('/owner/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_is_redirected_to_admin_dashboard_on_login(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_owner_is_redirected_to_owner_dashboard_on_login(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $response = $this->actingAs($owner)->get('/dashboard');
        $response->assertRedirect('/owner/dashboard');
    }

    public function test_customer_redirected_to_customer_dashboard_on_login(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $response = $this->actingAs($customer)->get('/dashboard');
        $response->assertRedirect('/customer/dashboard');
    }

    public function test_unauthenticated_user_is_redirected_to_login_from_protected_routes(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_customer_can_switch_to_owner_role(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->post(route('switch.to.owner'));

        $this->assertSame('owner', $user->fresh()->role);
        $response->assertRedirect(route('owner.cafe.create'));
    }

    public function test_owner_with_cafe_switching_to_owner_redirects_to_owner_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        Cafe::factory()->create(['owner_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('switch.to.owner'));

        $this->assertSame('owner', $user->fresh()->role);
        $response->assertRedirect(route('owner.dashboard'));
    }

    public function test_owner_can_switch_to_customer_role(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($user)->post(route('switch.to.customer'));

        $this->assertSame('customer', $user->fresh()->role);
        $response->assertRedirect(route('customer.dashboard'));
    }
}
