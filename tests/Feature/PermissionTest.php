<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'employees.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'departments.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'roles.view', 'guard_name' => 'web']);

        $viewer = Role::create(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions(['employees.view']);
    }

    public function test_user_without_permission_receives_forbidden_response(): void
    {
        $user = User::factory()->create();
        $user->assignRole('viewer');

        Sanctum::actingAs($user);

        $this->getJson('/api/roles')
            ->assertForbidden()
            ->assertJson([
                'message' => 'User does not have the right permissions.',
            ]);
    }

    public function test_employee_viewer_can_load_department_options(): void
    {
        $user = User::factory()->create();
        $user->assignRole('viewer');

        Sanctum::actingAs($user);

        $this->getJson('/api/departments/options')
            ->assertOk();
    }
}
