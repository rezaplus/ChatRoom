<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Admin', 'User', 'Guest'];

        foreach ($roles as $role) {

            // skip if role already exists
            if (Role::where('name', $role)->exists()) {
                continue;
            }

            Role::create(['name' => $role]);
        }
    }
}
