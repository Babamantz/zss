<?php

namespace Modules\HRM\Enums;


enum EmploymentType: string
{
    case PERMANENT = 'permanent';
    case CONTRACT = 'contract';
    case CASUAL = 'casual';
    case INTERN = 'intern';

    public static function all(): array
    {
        return [
            self::PERMANENT,
            self::CONTRACT,
            self::CASUAL,
            self::INTERN,
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
        // returns ['permanent', 'contract', 'casual', 'intern']
    }

    public static function isPermanent(string $type): bool
    {
        return $type === self::PERMANENT->value;
    }

    public static function labelOf(?string $value): string
    {
        return self::tryFrom($value)?->label() ?? '';
    }


    public function label(): string
    {
        return match ($this) {
            self::PERMANENT => 'Permanent',
            self::CONTRACT => 'Contract',
            self::CASUAL => 'Casual',
            self::INTERN => 'Intern',
        };
    }
}
