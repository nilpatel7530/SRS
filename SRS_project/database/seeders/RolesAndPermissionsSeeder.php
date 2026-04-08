<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        // Permission::create(['name' => 'edit articles']);

        // create roles and assign existing permissions
        $role1 = Role::firstOrCreate(['name' => 'User']);

        $role2 = Role::firstOrCreate(['name' => 'Admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::firstOrCreate([
            'email' => 'user@example.com'
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password')
        ]);
        $user->assignRole($role1);

        $admin = \App\Models\User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password')
        ]);
        $admin->assignRole($role2);
    }
}
