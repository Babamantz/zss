<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HRM\Models\Employee;
// use Modules\PAYROLL\Database\Factories\EmployeeComponentFactory;

class EmployeeComponent extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'employee_id',
        'component_id',
        'custom_amount',
        'is_recurring',
        'ends_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'ends_at'      => 'date',
        'is_recurring' => 'boolean',
        'custom_amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function component()
    {
        return $this->belongsTo(SalaryComponent::class, 'component_id');
    }

    // Only active components — not expired
    public function scopeActive($query)
    {
        return $query->where(
            fn($q) =>
            $q->whereNull('ends_at')->orWhere('ends_at', '>=', now())
        );
    }
}
