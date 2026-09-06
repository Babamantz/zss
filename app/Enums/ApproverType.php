<?php

namespace App\Enums;

enum ApproverType :string
{
    //
    case Role = 'role';
    case User = 'user';
    case DepartmentHead = 'department_head';
    case DG = 'director_general';
    case UnitHead = 'unit_head';
}
