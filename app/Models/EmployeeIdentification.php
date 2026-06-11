<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\HRM\Models\Employee;

class EmployeeIdentification extends Model
{
    //
    protected $guarded = false;

    public function employeeIdentifications()
    {
        return $this->belongsTo(Employee::class);
    }
}
