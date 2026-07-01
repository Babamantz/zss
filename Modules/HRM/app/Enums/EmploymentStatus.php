<?php

namespace Modules\HRM\Enums;

enum EmploymentStatus: string
{

    const ACTIVE   = 'active';
    const INACTIVE = 'in-active';

    const ALL = [self::ACTIVE, self::INACTIVE];
}
