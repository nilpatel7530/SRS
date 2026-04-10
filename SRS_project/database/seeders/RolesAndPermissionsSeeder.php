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

        // create roles
        $userRole = Role::firstOrCreate(['name' => 'User']);
        $tlRole = Role::firstOrCreate(['name' => 'TL']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // create demo users
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password')
        ]);
        $admin->assignRole($adminRole);

        $manager = User::firstOrCreate([
            'email' => 'manager@example.com'
        ], [
            'name' => 'Project Manager',
            'password' => Hash::make('password'),
            'manager_id' => $admin->id
        ]);
        $manager->assignRole($managerRole);

        $tl = User::firstOrCreate([
            'email' => 'tl@example.com'
        ], [
            'name' => 'Team Lead',
            'password' => Hash::make('password'),
            'manager_id' => $manager->id
        ]);
        $tl->assignRole($tlRole);

        $user = User::firstOrCreate([
            'email' => 'user@example.com'
        ], [
            'name' => 'Standard User',
            'password' => Hash::make('password'),
            'manager_id' => $tl->id
        ]);
        $user->assignRole($userRole);
    }
}
