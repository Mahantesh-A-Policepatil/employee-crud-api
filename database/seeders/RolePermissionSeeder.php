<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionNames = [
            'employees.view',
            'employees.create',
            'employees.update',
            'employees.delete',
            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',
            'roles.view',
            'roles.manage',
            'permissions.view',
            'permissions.manage',
        ];

        foreach ($permissionNames as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $nonAdmin = Role::firstOrCreate([
            'name' => 'non-admin',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions(Permission::all());
        $nonAdmin->syncPermissions([
            'employees.view',
            'departments.view',
        ]);

        $firstUser = User::orderBy('id')->first();

        if ($firstUser && ! $firstUser->hasAnyRole(['admin', 'non-admin'])) {
            $firstUser->assignRole($admin);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
