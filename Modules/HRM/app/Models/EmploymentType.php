<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PAYROLL\Models\SalaryComponent;

// use Modules\HRM\Database\Factories\EmploymentTypeFactory;

class EmploymentType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name'];

    public function employeesEducationType()
    {
        return $this->hasMany(Employee::class);
    }

    
   
    // protected static function newFactory(): EmploymentTypeFactory
    // {
    //     // return EmploymentTypeFactory::new();
    // }
}
