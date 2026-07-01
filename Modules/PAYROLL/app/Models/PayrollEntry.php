<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HRM\Models\Employee;
use Modules\PAYROLL\Models\PayPeriod;
// use Modules\PAYROLL\Database\Factories\PayrollEntryFactory;

class PayrollEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $guarded = false;


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function payPeriod()
    {
        return $this->belongsTo(PayPeriod::class);
    }
    public function items()
    {
        return $this->hasMany(PayrollEntryItem::class);
    }

    public function isDraft(): bool
    {
        return $this->payPeriod?->status === 'Draft';
    }


    // public static function getBaseSalaryForEntry(self $entry): float
    // {
    //     return (float) $entry->items()
    //         ->whereNull('component_id')
    //         ->value('finalized_amount') ?? 0;
    // }

    public static function getBaseSalaryForEntry(self $entry): float
    {
        return (float) $entry->items()
            ->whereNull('component_id')
            ->value('finalized_amount');
    }


    // protected static function newFactory(): PayrollEntryFactory
    // {
    //     // return PayrollEntryFactory::new();
    // }
}
