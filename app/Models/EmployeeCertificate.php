<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\HRM\Models\Employee;

class EmployeeCertificate extends Model
{
    //
    protected $guarded = false;

    public function employeeCertificates()
    {
        return $this->belongsTo(Employee::class);
    }

    
}
