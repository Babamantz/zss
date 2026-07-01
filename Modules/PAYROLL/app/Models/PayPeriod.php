<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PAYROLL\Enums\PayPeriodStatus;
use Modules\PAYROLL\Models\PayrollEntry;
// use Modules\PAYROLL\Database\Factories\PayPeriodFactory;

class PayPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'status',
        'total_gross',
        'total_net',
        'total_allowances',
        'total_deductions',
        'total_cost',
        'employee_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'total_gross'      => 'decimal:2',
        'total_net'        => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_cost'       => 'decimal:2',
        'employee_count'   => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function entries()
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // public function chainTransaction()
    // {
    //     return $this->morphOne(ChainTransaction::class, 'transactionable');
    // }

    // ── Status helpers ────────────────────────────────────────────────────────

    public function isDraft(): bool
    {
        return $this->status === PayPeriodStatus::DRAFT;
    }
    public function isProcessing(): bool
    {
        return $this->status === PayPeriodStatus::PROCESSING;
    }
    public function isPendingApproval(): bool
    {
        return $this->status === PayPeriodStatus::PENDING_APPROVAL;
    }
    public function isApproved(): bool
    {
        return $this->status === PayPeriodStatus::APPROVED;
    }
    public function isRejected(): bool
    {
        return $this->status === PayPeriodStatus::REJECTED;
    }
    public function isLocked(): bool
    {
        return $this->status === PayPeriodStatus::LOCKED_COMPLETED;
    }

    public function canBeSubmitted(): bool
    {
        return $this->isProcessing() && $this->entries()->exists();
    }

    public function canBeLocked(): bool
    {
        return $this->isApproved();
    }

    public function statusLabel(): string
    {
        return PayPeriodStatus::label($this->status);
    }

    public function statusBadgeColor(): string
    {
        return PayPeriodStatus::badgeColor($this->status);
    }

    // ── Chain-aware accessors ─────────────────────────────────────────────────

    // public function getApprovalHistoryAttribute()
    // {
    //     return $this->chainTransaction?->approvals()
    //         ->with('actionedBy', 'flowLevel')
    //         ->orderBy('level_no')
    //         ->get();
    // }

    // public function getRejectionReasonAttribute(): ?string
    // {
    //     return $this->chainTransaction?->approvals()
    //         ->where('status', \App\Enums\ChainStatus::REJECTED)
    //         ->value('rejection_reason');
    // }

    // ── Recompute cached summary from entries ─────────────────────────────────

    public function recomputeSummary(): void
    {
        $summary = $this->entries()
            ->selectRaw('
                COUNT(*)                as employee_count,
                SUM(total_gross)        as total_gross,
                SUM(net_pay)            as total_net,
                SUM(total_deductions)   as total_deductions
            ')
            ->first();

        // Allowances = gross - base salary sum
        // Cost = gross + any employer contributions (ZSSF employer side etc.)
        // For now: allowances = gross - base_salary_sum, cost = gross
        $baseSalarySum = $this->entries()
            ->join('payroll_entry_items', 'payroll_entries.id', '=', 'payroll_entry_items.payroll_entry_id')
            ->whereNull('payroll_entry_items.component_id')  // base salary line has null component_id
            ->sum('payroll_entry_items.finalized_amount');

        $this->update([
            'employee_count'   => $summary->employee_count ?? 0,
            'total_gross'      => $summary->total_gross    ?? 0,
            'total_net'        => $summary->total_net      ?? 0,
            'total_allowances' => max(0, ($summary->total_gross ?? 0) - $baseSalarySum),
            'total_deductions' => $summary->total_deductions ?? 0,
            'total_cost'       => $summary->total_gross    ?? 0, // extend when employer NSSF added
        ]);
    }
}
