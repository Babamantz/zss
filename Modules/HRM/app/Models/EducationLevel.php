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

    public function employee_education_levels()
    {
        return $this->belongsTo(Employee::class);
    }


    // protected static function newFactory(): EducationLevelFactory
    // {
    //     // return EducationLevelFactory::new();
    // }
}
