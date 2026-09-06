<?php

namespace Modules\HRM\Enums;

enum EmployeeHRVerification: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
}
