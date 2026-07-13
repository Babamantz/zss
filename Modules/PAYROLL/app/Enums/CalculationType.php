<?php

namespace Modules\PAYROLL\Enums;

enum CalculationType: string
{
    const AUTO      = 'auto';
    const FIXED      = 'fixed';
    const PERCENTAGE = 'percentage';

    const ALL = [self::FIXED, self::PERCENTAGE,self::AUTO];

    /**
     * Fixed is always superior over percentage.
     */
    public static function isFixed(string $type): bool
    {
        return $type === self::FIXED;
    }
    public static function isPercentage(string $type): bool
    {
        return $type === self::PERCENTAGE;
    }
}
