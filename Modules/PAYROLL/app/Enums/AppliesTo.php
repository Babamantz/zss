<?php

namespace Modules\PAYROLL\Enums;

enum AppliesTo: string
{

    const ALL           = 'all';
    const PERMANENT     = 'permanent';
    const NON_PERMANENT = 'non_permanent';

    const OPTIONS = [self::ALL, self::PERMANENT, self::NON_PERMANENT];
}
