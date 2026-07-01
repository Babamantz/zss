<?php

namespace App\Enums;

enum ChainStatus
{
    //
    const PENDING   = 'Pending';
    const APPROVED  = 'Approved';
    const REJECTED  = 'Rejected';
    const RETURNED  = 'Returned';
    const COMPLETED = 'Completed';

    public static function all()
    {
        return [
            self::PENDING,
            self::APPROVED,
            self::REJECTED,
            self::RETURNED,
            self::COMPLETED,
        ];
    }

    public static function badgeColor(string $status): string
    {
        return match ($status) {
            self::PENDING   => 'warning',
            self::APPROVED  => 'success',
            self::REJECTED  => 'danger',
            self::RETURNED  => 'info',
            self::COMPLETED => 'primary',
            default         => 'secondary',
        };
    }

    public static function label(string $status): string
    {
        return match ($status) {
            self::PENDING   => 'Pending Approval',
            self::APPROVED  => 'Approved',
            self::REJECTED  => 'Rejected',
            self::RETURNED  => 'Returned for Correction',
            self::COMPLETED => 'Completed',
            default         => ucfirst($status),
        };
    }
}
