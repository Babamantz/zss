<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\HRM\Models\Employee;

class Identification extends Model
{
    //
    protected $guarded = false;

    public function identificationTypes() 
    {
        return $this->hasMany(EmployeeIdentification::class);
    }

  
}
