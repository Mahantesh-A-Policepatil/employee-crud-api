<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InitialAdminPermissionSeeder extends Seeder
{
    /**
     * Grant every permission to the admin role.
     *
     * Users with the admin role inherit these permissions automatically, so
     * permissions do not need to be copied directly to individual users.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions(Permission::query()->get());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
