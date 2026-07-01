<?php

namespace Modules\HRM\Enums;

enum MaritalStatus: string
{

    const SINGLE   = 'single';
    const MARRIED  = 'married';
    const DIVORCED = 'divorced';

    const ALL = [self::SINGLE, self::MARRIED, self::DIVORCED];
}
