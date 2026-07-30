<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\EmployeeEducationLevel;

class EducationLevel extends Model
{
    //
    public $guarded = false;

    public function employee_employed_education()
    {
        return $this->hasOne(Employee::class, 'certificate_name_id');
    }
    public function level_names()
    {
        return $this->hasMany(EmployeeEducationLevel::class, 'certificate_name_id');
    }
}
