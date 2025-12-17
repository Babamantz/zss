<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'zhc-hq',
                'origin' => 'unguja',
                'slug' => 'zhc-unguja',
            ],
            [
                'name' => 'zhc-pemba',
                'origin' => 'pemba',
                'slug' => 'zhc-pemba',
            ],
        ];

        foreach ($tenants as $tenant) {
            Tenant::create([
                'name'   => $tenant['name'],
                'origin' => $tenant['origin'],
                'slug'   => $tenant['slug'],
            ]);
        }
    }
}
