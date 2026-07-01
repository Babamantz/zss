<?php

namespace Modules\PAYROLL\Enums;

enum ComponentType: string
{
    const EARNING   = 'Earning';
    const DEDUCTION = 'Deduction';

    const ALL = [self::EARNING, self::DEDUCTION];
}
