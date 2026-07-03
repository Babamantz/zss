<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PAYROLL\Enums\ComponentType;

// use Modules\PAYROLL\Database\Factories\ComponentFactory;

class Component extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'type'
    ];

    public function salaryComponents()
    {
        $this->hasMany(SalaryComponent::class);
    }
    public function scopeActive($q)
    {
        return $q->where('active', true);
    }
    public function employeeComponents()
    {
        $this->hasMany(SalaryComponent::class);
    }
    public function payrollEntryItem()
    {
        $this->hasMany(SalaryComponent::class);
    }
    public function isEarning(): bool
    {
        return $this->type === ComponentType::EARNING;
    }

    public function isDeduction(): bool
    {
        return $this->type === ComponentType::DEDUCTION;
    }
    // protected static function newFactory(): ComponentFactory
    // {
    //     // return ComponentFactory::new();
    // }
}
