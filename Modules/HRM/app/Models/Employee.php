<?php

namespace Modules\HRM\Models;

use App\Models\EmployeeCertificate;
use App\Models\EmployeeIdentification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HRM\Enums\EmploymentStatus;
use Modules\HRM\Enums\EmploymentType;
use Modules\HRM\Enums\Gender;
use Modules\HRM\Enums\MaritalStatus;
use Modules\HRM\Models\EmployeeBankAccount;
use Modules\HRM\Models\EmployeeEducationLevel;
use Modules\HRM\Models\EmploymentType as ModelsEmploymentType;
use Modules\PAYROLL\Models\EmployeeComponent;
use Modules\PAYROLL\Models\EmployeeFinanceProfile;
use Modules\PAYROLL\Models\PayrollEntry;

// use Modules\HRM\Database\Factories\EmployeeFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;



    /**
     * The attributes that are mass assignable.
     */

    protected $casts = [
        'contacts' => 'array'
    ];

    public function employmentType()
    {
        return $this->belongsTo(ModelsEmploymentType::class);
    }

    protected $guarded = false;
    public static function validEmploymentTypes(): array
    {
        return EmploymentType::ALL;
    }

    public static function validStatuses(): array
    {
        return EmploymentStatus::ALL;
    }

    public static function validGenders(): array
    {
        return Gender::ALL;
    }

    public static function validMaritalStatuses(): array
    {
        return MaritalStatus::ALL;
    }



    public function isPermanent(): bool
    {
        return $this->employment_type === EmploymentType::PERMANENT;
    }

    public function isActive(): bool
    {
        return $this->is_active === EmploymentStatus::ACTIVE;
    }

    public function employmentTypeLabel(): string
    {
        return EmploymentType::labelOf($this->employment_type ?? EmploymentType::PERMANENT);
    }

    public function educationLabel(): string
    {
        return EducationLevel::labelOf($this->education ?? '');
    }

    public function employeeComponents()
    {
        return $this->hasMany(EmployeeComponent::class);
    }
   
    public function financeProfile()
    {
        return $this->hasOne(EmployeeFinanceProfile::class);
    }


    public function getBaseSalaryAttribute(): ?string
    {
        return $this->financeProfile?->base_salary;
    }

    public function getFinanceBankAccountNumberAttribute(): ?string

    {
        return $this->financeProfile?->account_no;
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
        return $this->hasMany(EmployeeEducationLevel::class);
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
