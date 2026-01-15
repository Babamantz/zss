<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\HRM\Database\Factories\UnitFactory;

class Unit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = false;


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employees()
    {

        return $this->hasMany(Employee::class, 'unit_id');
    }

    // protected static function newFactory(): UnitFactory
    // {
    //     // return UnitFactory::new();
    // }
}
