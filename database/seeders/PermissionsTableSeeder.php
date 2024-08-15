<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define permissions
        $permissions = [
            'view chat rooms',       // For guests
            'send messages',         // For users and admins
            'delete own messages',   // For users
            'request join chat room', // For users
            'accept users',          // For admins
            'delete any message',    // For admins
            'create chat room',      // For admins
            'delete chat room',      // For admins
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $guestRole = Role::where('name', 'Guest')->first();
        $userRole = Role::where('name', 'User')->first();
        $adminRole = Role::where('name', 'Admin')->first();

        $guestPermissions = ['view chat rooms'];
        $userPermissions = array_merge(['send messages', 'delete own messages', 'request join chat room'], $guestPermissions);
        $adminPermissions = array_merge(['send messages', 'accept users', 'delete any message', 'create chat room', 'delete chat room'], $userPermissions);


        foreach ($guestPermissions as $permission) {
            $guestRole->permissions()->attach(Permission::where('name', $permission)->first());
        }

        foreach ($userPermissions as $permission) {
            $userRole->permissions()->attach(Permission::where('name', $permission)->first());
        }

        foreach ($adminPermissions as $permission) {
            $adminRole->permissions()->attach(Permission::where('name', $permission)->first());
        }
    }
}
