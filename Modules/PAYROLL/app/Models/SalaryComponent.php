<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PAYROLL\Models\EmployeeComponent;
// use Modules\PAYROLL\Database\Factories\SalaryComponentFactory;

class SalaryComponent extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'is_global',
        'created_by',
        'updated_by',
    ];

    // type: 'Earning' | 'Deduction'
    public function scopeEarnings($query)
    {
        return $query->where('type', 'Earning');
    }
    public function scopeDeductions($query)
    {
        return $query->where('type', 'Deduction');
    }
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    public function employeeComponents()
    {
        return $this->hasMany(EmployeeComponent::class, 'component_id');
    }
}
