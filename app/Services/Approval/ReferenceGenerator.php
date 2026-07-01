<?php
namespace App\Services\Approval;

use App\Models\ChainModule;

class ReferenceGenerator {
       public function generate(ChainModule $module): string
    {
        $prefix = strtoupper($module->code);

        $today = now()->format('Ymd');

        $count = $module->approvalRequests()
            ->whereDate('created_at', today())
            ->count() + 1;

        return sprintf(
            '%s-%s-%06d',
            $prefix,
            $today,
            $count
        );
    }
}