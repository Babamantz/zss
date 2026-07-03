<?php

namespace Modules\PAYROLL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PAYROLL\Models\Component;
// use Modules\PAYROLL\Database\Factories\PayrollEntryItemFactory;

class PayrollEntryItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = false;

    public function entry()
    {
        return $this->belongsTo(PayrollEntry::class, 'payroll_entry_id');
    }
    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    // protected static function newFactory(): PayrollEntryItemFactory
    // {
    //     // return PayrollEntryItemFactory::new();
    // }
}
