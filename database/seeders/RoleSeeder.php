<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{


    public function run(): void
    {
        // Define an array of roles to generate
        $roles = [
            'admin',
            'super-admin',
            'hr-officer',
            'director-hr'
        ];

        // Loop and safely create each record
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
