<?php

namespace Modules\HRM\Models;

use App\Models\EducationLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\HRM\Database\Factories\EmployeeEdutactionLevelFactory;

class EmployeeEducationLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = false;

    public function education_level_name()
    {
        return $this->belongsTo(EducationLevel::class,'certificate_name_id');
    }
  
    public function employee_education_levels()
    {
        return $this->belongsTo(Employee::class);
    }
}
