<?php

namespace Modules\PAYROLL\Enums;

enum CalculationType: string
{
    const FIXED      = 'fixed';
    const PERCENTAGE = 'percentage';

    const ALL = [self::FIXED, self::PERCENTAGE];

    /**
     * Fixed is always superior over percentage.
     */
    public static function isFixed(string $type): bool
    {
        return $type === self::FIXED;
    }
}
