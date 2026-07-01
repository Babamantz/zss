<?php

namespace Database\Seeders;

use App\Models\ApprovalStep;
use App\Models\ChainModule;
use App\Models\CustomerFlow;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ChainModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    
    {
        $module = ChainModule::create([
            'name'        => 'Customer Flow',
            'code'        => 'named_customer_flow',
            'description' => 'Standard two-level approval: Officer → HR',
            'is_active'   => true,
        ]);

        $officerRole = Role::firstOrCreate(['name' => 'officer']);
        $hrRole      = Role::firstOrCreate(['name' => 'hr_manager']);

        ApprovalStep::create([
            'chain_module_id' => $module->id,
            'level_no'        => 1,
            'level_name'      => 'Officer Review',
            'order'           => 1,
            'role_id'         => $officerRole->id,
            'is_active'       => true,
        ]);

        ApprovalStep::create([
            'chain_module_id' => $module->id,
            'level_no'        => 2,
            'level_name'      => 'HR Final Approval',
            'order'           => 2,
            'role_id'         => $hrRole->id,
            'is_active'       => true,
        ]);
    }
    
    
}
