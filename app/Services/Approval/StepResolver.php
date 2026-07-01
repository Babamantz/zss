<?php

namespace App\Services\Approval;

use App\Models\ApprovalStep;
use App\Models\ChainModule;

class StepResolver
{

    public function first(ChainModule $module): ApprovalStep
    {
        return $module->steps()
            ->where('is_active', true)
            ->orderBy('order')
            ->firstOrFail();
    }

    public function next(
        ChainModule $module,
        int $currentLevel
    ): ?ApprovalStep {

        return $module->steps()

            ->where('level_no', '>', $currentLevel)

            ->where('is_active', true)

            ->orderBy('level_no')

            ->first();
    }

    public function current(
        ChainModule $module,
        int $level
    ): ApprovalStep {

        return $module->steps()

            ->where('level_no', $level)

            ->firstOrFail();
    }
}
