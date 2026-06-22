<?php

namespace Modules\HRM\Models;

use App\Models\EmployeeCertificate;
use App\Models\EmployeeIdentification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\HRM\Models\EmployeeBankAccount;
use Modules\PAYROLL\Models\EmployeeComponent;
use Modules\PAYROLL\Models\EmployeeFinanceProfile;
use Modules\PAYROLL\Models\PayrollEntry;

// use Modules\HRM\Database\Factories\EmployeeFactory;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $casts = [
        'contacts' => 'array'
    ];

    protected $guarded = false;

    public function components()
    {
        return $this->hasMany(EmployeeComponent::class);
    }
    public function financeProfile()
    {
        return $this->hasOne(EmployeeFinanceProfile::class);
    }

    // Modules/HRM/Models/Employee.php

    // Add inside the Employee model alongside existing relationships:

    // public function financeProfile(): HasOne
    // {
    //     return $this->hasOne(EmployeeFinanceProfile::class);
    // }

    // Convenience: get base salary directly from profile
    public function getBaseSalaryAttribute(): ?string
    {
        return $this->financeProfile?->base_salary;
    }

    public function getBankAccountAttribute(): ?string
    {
        return $this->financeProfile?->bank_account_number;
    }
    public function payrollEntries()
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function certificates()
    {
        return $this->hasMany(EmployeeCertificate::class);
    }
    public function identifications()
    {
        return $this->hasMany(EmployeeIdentification::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function education_levels()
    {
        return $this->hasMany(EducationLevel::class);
    }

    public function location()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function bankAccount()
    {
        return $this->hasOne(EmployeeBankAccount::class, 'employee_id');
    }

    // protected static function newFactory(): EmployeeFactory
    // {
    //     // return EmployeeFactory::new();
    // }
}
