<?php

namespace Modules\PAYROLL\Enums;

enum PayPeriodStatus: string
{
    case DRAFT = 'Draft';
    case PROCESSING = 'Processing';
    case PENDING_APPROVAL = 'Pending_Approval';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';
    case LOCKED_COMPLETED = 'Locked_Completed';


    public static function all()
    {
        return  [self::DRAFT, self::PROCESSING, self::PENDING_APPROVAL, self::APPROVED, self::REJECTED, self::LOCKED_COMPLETED];
    }

    public static function label(string $status): string
    {
        return match ($status) {
            self::DRAFT->value => 'Draft',
            self::PROCESSING->value => 'Processing',
            self::PENDING_APPROVAL->value => 'Pending Approval',
            self::APPROVED->value => 'Approved',
            self::REJECTED->value => 'Rejected',
            self::LOCKED_COMPLETED->value => 'Locked / Completed',
            default => ucfirst($status),
        };
    }

    public static function badgeColor(string $status): string
    {
        return match ($status) {
            self::DRAFT->value => 'secondary',
            self::PROCESSING->value => '',
            self::PENDING_APPROVAL->value => 'warning',
            self::APPROVED->value => 'primary',
            self::REJECTED->value => 'danger',
            self::LOCKED_COMPLETED->value => 'success',
            default => '',
        };
    }
}
