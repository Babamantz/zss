<?php

// Modules/PAYROLL/Enums/ReportType.php

namespace Modules\PAYROLL\Enums;

class ReportType
{
    const SDL             = 'sdl';
    const PAYE            = 'paye';
    const NSSF            = 'nssf';
    const PAYROLL_SUMMARY = 'payroll_summary';
    const DEDUCTIONS      = 'deductions';

    const ALL = [
        self::SDL,
        self::PAYE,
        self::NSSF,
        self::PAYROLL_SUMMARY,
        self::DEDUCTIONS,
    ];

    public static function label(string $type): string
    {
        return match ($type) {
            self::SDL             => 'SDL — Skills Development Levy',
            self::PAYE            => 'PAYE — Pay As You Earn',
            self::NSSF            => 'NSSF — National Social Security',
            self::PAYROLL_SUMMARY => 'Payroll Summary',
            self::DEDUCTIONS      => 'Deductions Report',
            default               => ucfirst($type),
        };
    }

    public static function shortLabel(string $type): string
    {
        return match ($type) {
            self::SDL             => 'SDL',
            self::PAYE            => 'PAYE',
            self::NSSF            => 'NSSF',
            self::PAYROLL_SUMMARY => 'Summary',
            self::DEDUCTIONS      => 'Deductions',
            default               => ucfirst($type),
        };
    }

    public static function badgeColor(string $type): string
    {
        return match ($type) {
            self::SDL             => 'warning',
            self::PAYE            => 'danger',
            self::NSSF            => 'info',
            self::PAYROLL_SUMMARY => 'primary',
            self::DEDUCTIONS      => 'secondary',
            default               => 'light',
        };
    }

    public static function description(string $type): string
    {
        return match ($type) {
            self::SDL             => '5% of (base salary + allowances) per pay period',
            self::PAYE            => 'Income tax withheld from employee gross pay',
            self::NSSF            => 'Social security contributions',
            self::PAYROLL_SUMMARY => 'Full payroll summary across all employees',
            self::DEDUCTIONS      => 'All deduction components breakdown',
            default               => '',
        };
    }
}
