<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\HRM\Database\Factories\EmployeeBankAccountFactory;

class EmployeeBankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $guarded = false;

    public function employeeAccount()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function bank()
    {
       return $this->belongsTo(Bank::class);
    }

    // protected static function newFactory(): EmployeeBankAccountFactory
    // {
    //     // return EmployeeBankAccountFactory::new();
    // }
}
