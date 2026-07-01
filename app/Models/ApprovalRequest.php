<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    //


    protected $fillable = [

        'chain_module_id',

        'reference_no',

        'title',

        'description',

        'status',

        'current_level',

        'total_levels',

        'created_by',

        'assigned_to_user',

        'submitted_at',

        'completed_at'

    ];

    public function approvable()
    {
        return $this->morphTo();
    }

    public function module()
    {
        return $this->belongsTo(
            ChainModule::class,
            'chain_module_id'
        );
    }

    public function actions()
    {
        return $this->hasMany(
            ApprovalAction::class
        );
    }

    public function assignedTo()
    {
        return $this->belongsTo(
            User::class,
            'assigned_to_user'
        );
    }
}
