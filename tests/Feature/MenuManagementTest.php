<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuManagementTest extends TestCase
{
    use RefreshDatabase;

    private function validCategoryData($overrides = [])
    {
        return array_merge([
            'name' => 'Main Course',
            'description' => 'Delicious main courses',
            'sort_order' => 10,
            'status' => 'active',
        ], $overrides);
    }

    private function validItemData($overrides = [])
    {
        return array_merge([
            'name' => 'Espresso',
            'description' => 'Strong coffee',
            'price' => 2.50,
            'is_available' => true,
            'sort_order' => 5,
            'status' => 'active',
        ], $overrides);
    }

    public function test_admin_can_create_a_category()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cafe = Cafe::factory()->create();

        $response = $this->actingAs($admin)->postJson("/cafes/{$cafe->id}/menu/categories", $this->validCategoryData());

        $response->assertStatus(201);
        $this->assertDatabaseHas('menu_categories', ['cafe_id' => $cafe->id, 'name' => 'Main Course']);
    }

    public function test_owner_can_create_a_category_for_their_cafe()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/menu/categories", $this->validCategoryData());

        $response->assertStatus(201);
        $this->assertDatabaseHas('menu_categories', ['cafe_id' => $cafe->id, 'name' => 'Main Course']);
    }

    public function test_customer_cannot_create_a_category()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/menu/categories", $this->validCategoryData());

        $response->assertStatus(403);
    }

    public function test_owner_can_update_their_own_category()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $category = MenuCategory::factory()->create(['cafe_id' => $cafe->id]);

        $response = $this->actingAs($owner)->putJson("/menu-categories/{$category->id}", $this->validCategoryData([
            'name' => 'Updated Category',
        ]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('menu_categories', ['id' => $category->id, 'name' => 'Updated Category']);
    }

    public function test_owner_cannot_update_another_owners_category()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe2 = Cafe::factory()->create(['owner_id' => $owner2->id]);
        $category = MenuCategory::factory()->create(['cafe_id' => $cafe2->id]);

        $response = $this->actingAs($owner1)->putJson("/menu-categories/{$category->id}", $this->validCategoryData());

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_their_own_category()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $category = MenuCategory::factory()->create(['cafe_id' => $cafe->id]);

        $response = $this->actingAs($owner)->deleteJson("/menu-categories/{$category->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($category);
    }

    public function test_owner_cannot_delete_another_owners_category()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe2 = Cafe::factory()->create(['owner_id' => $owner2->id]);
        $category = MenuCategory::factory()->create(['cafe_id' => $cafe2->id]);

        $response = $this->actingAs($owner1)->deleteJson("/menu-categories/{$category->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_create_a_menu_item()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cafe = Cafe::factory()->create();

        $response = $this->actingAs($admin)->postJson("/cafes/{$cafe->id}/menu/items", $this->validItemData());

        $response->assertStatus(201);
        $this->assertDatabaseHas('menu_items', ['cafe_id' => $cafe->id, 'name' => 'Espresso']);
    }

    public function test_owner_can_create_a_menu_item()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/menu/items", $this->validItemData());

        $response->assertStatus(201);
        $this->assertDatabaseHas('menu_items', ['cafe_id' => $cafe->id, 'name' => 'Espresso']);
    }

    public function test_customer_cannot_create_a_menu_item()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/menu/items", $this->validItemData());

        $response->assertStatus(403);
    }

    public function test_owner_can_update_their_own_menu_item()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $item = MenuItem::factory()->create(['cafe_id' => $cafe->id]);

        $response = $this->actingAs($owner)->putJson("/menu-items/{$item->id}", $this->validItemData([
            'name' => 'Updated Item',
        ]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('menu_items', ['id' => $item->id, 'name' => 'Updated Item']);
    }

    public function test_owner_cannot_update_another_owners_menu_item()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe2 = Cafe::factory()->create(['owner_id' => $owner2->id]);
        $item = MenuItem::factory()->create(['cafe_id' => $cafe2->id]);

        $response = $this->actingAs($owner1)->putJson("/menu-items/{$item->id}", $this->validItemData());

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_their_own_menu_item()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $item = MenuItem::factory()->create(['cafe_id' => $cafe->id]);

        $response = $this->actingAs($owner)->deleteJson("/menu-items/{$item->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($item);
    }

    public function test_owner_cannot_delete_another_owners_menu_item()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe2 = Cafe::factory()->create(['owner_id' => $owner2->id]);
        $item = MenuItem::factory()->create(['cafe_id' => $cafe2->id]);

        $response = $this->actingAs($owner1)->deleteJson("/menu-items/{$item->id}");

        $response->assertStatus(403);
    }

    public function test_duplicate_category_name_within_same_cafe_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        MenuCategory::factory()->create(['cafe_id' => $cafe->id, 'name' => 'Desserts']);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/menu/categories", $this->validCategoryData([
            'name' => 'Desserts',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_same_category_name_in_different_cafes_is_allowed()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe1 = Cafe::factory()->create(['owner_id' => $owner->id]);
        MenuCategory::factory()->create(['cafe_id' => $cafe1->id, 'name' => 'Desserts']);

        $cafe2 = Cafe::factory()->create(['owner_id' => $owner->id]);
        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe2->id}/menu/categories", $this->validCategoryData([
            'name' => 'Desserts',
        ]));

        $response->assertStatus(201);
    }

    public function test_invalid_price_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/menu/items", $this->validItemData([
            'price' => 'invalid_price',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('price');
    }

    public function test_negative_price_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/menu/items", $this->validItemData([
            'price' => -5.00,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('price');
    }

    public function test_invalid_status_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/menu/categories", $this->validCategoryData([
            'status' => 'archived',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }

    public function test_invalid_availability_value_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/menu/items", $this->validItemData([
            'is_available' => 'maybe',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('is_available');
    }

    public function test_invalid_category_id_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/menu/items", $this->validItemData([
            'menu_category_id' => 9999,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('menu_category_id');
    }

    public function test_category_from_another_cafe_cannot_be_assigned_to_a_menu_item()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe1 = Cafe::factory()->create(['owner_id' => $owner->id]);
        $cafe2 = Cafe::factory()->create(['owner_id' => $owner->id]);

        $categoryCafe2 = MenuCategory::factory()->create(['cafe_id' => $cafe2->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe1->id}/menu/items", $this->validItemData([
            'menu_category_id' => $categoryCafe2->id,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('menu_category_id');
    }

    public function test_owner_cannot_spoof_cafe_id()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe2 = Cafe::factory()->create(['owner_id' => $owner2->id]);

        $response = $this->actingAs($owner1)->postJson("/cafes/{$cafe2->id}/menu/categories", $this->validCategoryData());

        $response->assertStatus(403);
    }

    public function test_customer_sees_only_active_categories()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create(['status' => 'active']);
        MenuCategory::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active']);
        MenuCategory::factory()->create(['cafe_id' => $cafe->id, 'status' => 'inactive']);

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/menu/categories");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    public function test_customer_sees_only_active_and_available_menu_items()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create(['status' => 'active']);

        // Active and available
        MenuItem::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'is_available' => true]);

        // Inactive
        MenuItem::factory()->create(['cafe_id' => $cafe->id, 'status' => 'inactive', 'is_available' => true]);

        // Unavailable
        MenuItem::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'is_available' => false]);

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/menu/items");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    public function test_inactive_cafes_do_not_expose_menus()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create(['status' => 'inactive']);
        MenuItem::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'is_available' => true]);

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/menu/items");

        $response->assertStatus(403);
    }

    public function test_soft_deleted_categories_are_excluded()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create(['status' => 'active']);
        $category = MenuCategory::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active']);

        $category->delete();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/menu/categories");

        $response->assertStatus(200);
        $this->assertCount(0, $response->json());
    }

    public function test_soft_deleted_items_are_excluded()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create(['status' => 'active']);
        $item = MenuItem::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'is_available' => true]);

        $item->delete();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/menu/items");

        $response->assertStatus(200);
        $this->assertCount(0, $response->json());
    }

    public function test_sorting_by_category_item_sort_order_works()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create(['status' => 'active']);

        $item2 = MenuItem::factory()->create(['cafe_id' => $cafe->id, 'name' => 'B', 'sort_order' => 10]);
        $item1 = MenuItem::factory()->create(['cafe_id' => $cafe->id, 'name' => 'A', 'sort_order' => 5]);
        $item3 = MenuItem::factory()->create(['cafe_id' => $cafe->id, 'name' => 'C', 'sort_order' => 10]); // Same sort order, sorts by name

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/menu/items");

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertEquals('A', $data[0]['name']);
        $this->assertEquals('B', $data[1]['name']);
        $this->assertEquals('C', $data[2]['name']);
    }

    public function test_unauthenticated_users_cannot_access_protected_management_routes()
    {
        $cafe = Cafe::factory()->create();

        $response = $this->postJson("/cafes/{$cafe->id}/menu/categories", $this->validCategoryData());
        $response->assertStatus(401);

        $response = $this->postJson("/cafes/{$cafe->id}/menu/items", $this->validItemData());
        $response->assertStatus(401);
    }
}
