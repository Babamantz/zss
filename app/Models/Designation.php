<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\HRM\Models\Employee;

class Designation extends Model
{
    //
    protected $guarded = false;

    public function employeeDesignations()
    {
        return $this->hasMany(Employee::class);
    }
}
