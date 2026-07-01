<?php

namespace Modules\HRM\Enums;

enum Gender: string
{

    const MALE   = 'male';
    const FEMALE = 'female';

    const ALL = [self::MALE, self::FEMALE];
}
