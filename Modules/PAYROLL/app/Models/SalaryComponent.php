<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\HRM\Enums\EmploymentType;
use Modules\HRM\Models\EmploymentType as AppliesTo;
use Modules\PAYROLL\Enums\CalculationType;
use Modules\PAYROLL\Enums\ComponentType;
use Modules\PAYROLL\Models\Component;
// use Modules\PAYROLL\Database\Factories\SalaryComponentFactory;

class SalaryComponent extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'component_id',
        'type',
        'calculation_type',
        'percentage_value',
        'amount',
        'is_global',
        'applies_to',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_global'           => 'boolean',
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

    // public function isPaye(): bool
    // {
    //     return str_contains(Str::lower($this->component?->code ?? ''), 'paye');
    // }

    public function isPaye(): bool
    {
        return Str::contains($this->component?->code ?? '', 'paye', ignoreCase: true);
    }





    public function isAuto(): bool
    {
        return $this->calculation_type === CalculationType::AUTO;
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
    // public function computeAmount(float $base = 0): float
    // {
    //     if (CalculationType::isFixed($this->calculation_type)) {
    //         return (float) ($this->amount ?? 0);
    //     }

    //     return round($base * ((float) $this->percentage_value / 100), 2);
    // }
    public function computeAmount(float $base = 0): float
    {
        if ($this->isPaye()) {
            return self::calculatePaye($base);
        }

        if (CalculationType::isFixed($this->calculation_type)) {
            return (float) ($this->amount ?? 0);
        }

        return round($base * ((float) $this->percentage_value / 100), 2);
    }

    /**
     * Zanzibar/Tanzania-style monthly PAYE bracket calculation.
     *
     * Bands:
     *   <= 270,000                → 0
     *   270,000 - 520,000         → 8% of excess over 270,000
     *   520,001 - 760,000         → 20,000 + 20% of excess over 520,000
     *   760,001 - 1,000,000       → 68,000 + 25% of excess over 760,000
     *   > 1,000,000               → 128,000 + 30% of excess over 1,000,000
     */
    public static function calculatePaye(float $amount): float
    {
        return match (true) {
            $amount <= 270000  => 0.0,
            $amount <= 520000  => round(0.08 * ($amount - 270000), 2),
            $amount <= 760000  => round(20000 + 0.20 * ($amount - 520000), 2),
            $amount <= 1000000 => round(68000 + 0.25 * ($amount - 760000), 2),
            default             => round(128000 + 0.30 * ($amount - 1000000), 2),
        };
    }



    // ── Relationships ─────────────────────────────────────────────────────────





    public function appliesTo()
    {
        return $this->belongsTo(AppliesTo::class, 'applies_to');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function component()
    {
        return $this->belongsTo(Component::class,'component_id');
    }
}
