<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HRM\Models\Employee;

// use Modules\PAYROLL\Database\Factories\EmployeeFinanceProfileFactory;

class EmployeeFinanceProfile extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */


    protected $table = 'employee_finance_profiles';

    protected $fillable = [
        'employee_id',
        'base_salary',
        'bank_account_number',
        'tax_id',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeWithEmployee($query)
    {
        return $query->with('employee.user');
    }

    // protected static function newFactory(): EmployeeFinanceProfileFactory
    // {
    //     // return EmployeeFinanceProfileFactory::new();
    // }
}
