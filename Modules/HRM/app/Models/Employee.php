<?php

namespace Modules\HRM\Models;

use App\Models\Designation;
use App\Models\EmployeeCertificate;
use App\Models\EmployeeIdentification;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\FilterByTenant;
use App\Traits\FilterEmployeeByTenant;
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
use RingleSoft\LaravelProcessApproval\Contracts\ApprovableModel;
use RingleSoft\LaravelProcessApproval\Models\ProcessApproval;
use RingleSoft\LaravelProcessApproval\Traits\Approvable;

// use Modules\HRM\Database\Factories\EmployeeFactory;

class Employee extends Model implements ApprovableModel
{
    use HasFactory, SoftDeletes, FilterEmployeeByTenant, Approvable;


    protected $guarded = false;


    /**
     * The attributes that are mass assignable.
     */

    protected $casts = [
        'contacts' => 'array',
        'is_disable' => 'boolean',
        'is_hr_registered' => 'boolean',
        'is_officer' => 'boolean',
        'disability_types' => 'array'

    ];


    // No separate submit step — hr-officer's create IS the submission
    public function enableAutoSubmit(): bool
    {
        return true;
    }

    public function onApprovalCompleted(ProcessApproval $approval): bool
    {
        $this->is_hr_registered = true;
        $this->is_active = true;
        $this->save();
        return true;
    }


    public function employmentType()
    {
        return $this->belongsTo(ModelsEmploymentType::class);
    }

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
        return $this->hasMany(EmployeeIdentification::class,'employee_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function employed_education_level()
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id');
    }
    public function education_levels()
    {
        return $this->hasMany(EmployeeEducationLevel::class,'employee_id');
    }

    public function location()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
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
