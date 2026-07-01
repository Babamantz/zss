<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PAYROLL\Enums\AppliesTo;
use Modules\PAYROLL\Enums\CalculationType;
use Modules\PAYROLL\Enums\ComponentType;
use Modules\PAYROLL\Models\EmployeeComponent;
// use Modules\PAYROLL\Database\Factories\SalaryComponentFactory;

class SalaryComponent extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'type',
        'calculation_type',
        'percentage_value',
        'amount',
        'is_global',
        'is_global_component',
        'applies_to',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_global'           => 'boolean',
        'is_global_component' => 'boolean',
        'percentage_value'    => 'decimal:4',
    ];

    // ── Validation helpers ────────────────────────────────────────────────────

    public static function validTypes(): array
    {
        return ComponentType::ALL;
    }

    public static function validCalculationTypes(): array
    {
        return CalculationType::ALL;
    }

    public static function validAppliesToOptions(): array
    {
        return AppliesTo::OPTIONS;
    }

    // ── Type helpers ──────────────────────────────────────────────────────────

    public function isEarning(): bool
    {
        return $this->type === ComponentType::EARNING;
    }

    public function isDeduction(): bool
    {
        return $this->type === ComponentType::DEDUCTION;
    }

    public function isFixed(): bool
    {
        return $this->calculation_type === CalculationType::FIXED;
    }

    public function isPercentage(): bool
    {
        return $this->calculation_type === CalculationType::PERCENTAGE;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeEarnings($q)
    {
        return $q->where('type', ComponentType::EARNING);
    }

    public function scopeDeductions($q)
    {
        return $q->where('type', ComponentType::DEDUCTION);
    }

    public function scopeGlobalComponents($q)
    {
        return $q->where('is_global_component', true);
    }

    public function scopeNormalComponents($q)
    {
        return $q->where('is_global_component', false);
    }

    public function scopeForEmploymentType($q, string $type)
    {
        return $q->where(
            fn($w) =>
            $w->where('applies_to', AppliesTo::ALL)
                ->orWhere('applies_to', $type)
        );
    }

    // ── Amount computation ────────────────────────────────────────────────────

    /**
     * Fixed is always superior over percentage.
     */
    public function computeAmount(float $base = 0): float
    {
        if (CalculationType::isFixed($this->calculation_type)) {
            return (float) ($this->custom_amount ?? 0);
        }

        return round($base * ((float) $this->percentage_value / 100), 2);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function employeeComponents()
    {
        return $this->hasMany(EmployeeComponent::class, 'component_id');
    }
}
