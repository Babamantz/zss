<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HRM\Models\Employee;

class Certificate extends Model
{
    //

    protected $guarded = false;

    public function employeeDesignation()
    {
        return $this->belongsTo(Employee::class);
    }
}
