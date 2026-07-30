<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\HRM\Database\Factories\EducationLevelFactory;

class EducationLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = false;


    public function employee_employed_education()
    {
        return $this->hasOne(Employee::class, 'education_level_id');
    }
    public function level_names()
    {
        return $this->hasMany(EmployeeEducationLevel::class, 'certificate_name_id');
    }



    // protected static function newFactory(): EducationLevelFactory
    // {
    //     // return EducationLevelFactory::new();
    // }
}
