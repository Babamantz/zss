<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HRM\Models\Employee;
use Modules\PAYROLL\Enums\CalculationType;
// use Modules\PAYROLL\Database\Factories\EmployeeComponentFactory;

class EmployeeComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'component_id',
        'custom_amount',
        'calculation_type',
        'percentage_value',
        'is_recurring',
        'ends_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'ends_at'          => 'date',
        'is_recurring'     => 'boolean',
        'custom_amount'    => 'decimal:2',
        'percentage_value' => 'decimal:4',
    ];

    public static function validCalculationTypes(): array
    {
        return CalculationType::ALL;
    }

    public function isFixed(): bool
    {
        return $this->calculation_type === CalculationType::FIXED;
    }

    public function scopeActive($q)
    {
        return $q->where(
            fn($w) =>
            $w->whereNull('ends_at')->orWhere('ends_at', '>=', now())
        );
    }

    /**
     * Fixed is always superior over percentage.
     */
    public function computeAmount(float $base = 0): float
    {
        if (CalculationType::isFixed($this->calculation_type)) {
            return (float) $this->custom_amount;
        }

        return round($base * ((float) $this->percentage_value / 100), 2);
    }

    public function employee()
    {
        return $this->belongsTo(\Modules\HRM\Models\Employee::class);
    }

    public function component()
    {
        return $this->belongsTo(SalaryComponent::class, 'component_id');
    }

   
}
