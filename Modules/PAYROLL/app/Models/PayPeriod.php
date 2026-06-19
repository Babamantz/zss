<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PAYROLL\Database\Factories\PayPeriodFactory;

class PayPeriod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */


    // Modules/HRM/Models/PayPeriod.php

    protected $fillable = [
        'start_date',
        'end_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // status: 'Draft' | 'Processing' | 'Locked_Completed'
    public function isLocked(): bool
    {
        return $this->status === 'Locked_Completed';
    }
    public function isDraft(): bool
    {
        return $this->status === 'Draft';
    }

    public function entries()
    {
        return $this->hasMany(PayrollEntry::class);
    }
}
