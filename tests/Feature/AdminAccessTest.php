<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'admin']);
    }

    public function test_guest_is_redirected_to_login_page_when_accessing_admin(): void
    {
        $response = $this->get('/admin/bookings');

        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/bookings');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/bookings');

        $response->assertStatus(200);
    }
}