<?php

namespace Database\Seeders;

use App\Models\NavigationItem;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class NavigationPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['key' => 'employees', 'label' => 'Employee Management', 'path' => '/', 'icon' => '💼', 'sort_order' => 10],
            ['key' => 'attendance', 'label' => 'Attendance Management', 'path' => '/attendance', 'icon' => '📅', 'sort_order' => 20],
            ['key' => 'projects', 'label' => 'Project Management', 'path' => '/projects', 'icon' => '📁', 'sort_order' => 30],
            ['key' => 'departments', 'label' => 'Department Management', 'path' => '/departments', 'icon' => '🏢', 'sort_order' => 40],
            ['key' => 'roles', 'label' => 'Role Management', 'path' => '/roles', 'icon' => '🔑', 'sort_order' => 50],
            ['key' => 'user-roles', 'label' => 'User Role Assignment', 'path' => '/user-roles', 'icon' => '✓', 'sort_order' => 60],
        ];

        foreach ($items as $item) {
            NavigationItem::updateOrCreate(
                ['key' => $item['key']],
                $item + ['is_active' => true],
            );

            foreach (['create', 'read', 'update', 'delete'] as $operation) {
                Permission::firstOrCreate([
                    'name' => "{$item['key']}.{$operation}",
                    'guard_name' => 'web',
                ]);
            }
        }

        $admin = Role::findByName('admin', 'web');
        $admin->syncPermissions(Permission::all());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
