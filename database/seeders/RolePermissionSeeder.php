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
            'employees.read',
            'employees.create',
            'employees.update',
            'employees.delete',
            'departments.read',
            'departments.create',
            'departments.update',
            'departments.delete',
            'projects.read',
            'projects.create',
            'projects.update',
            'projects.delete',
            'roles.read',
            'roles.create',
            'roles.update',
            'roles.delete',
            'attendance.read',
            'attendance.create',
            'attendance.update',
            'attendance.delete',
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
            'employees.read',
            'departments.read',
        ]);

        $firstUser = User::orderBy('id')->first();

        if ($firstUser && ! $firstUser->hasAnyRole(['admin', 'non-admin'])) {
            $firstUser->assignRole($admin);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
