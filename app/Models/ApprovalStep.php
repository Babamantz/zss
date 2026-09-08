<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalStep extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'approval_chain_id',
        'level_no',
        'level_name',
        'order',
        'role_id',
        'user_id',
        'is_active'
    ];

    public function module()
    {
        return $this->belongsTo(
            ApprovalChain::class,
            'approval_chain_id'
        );
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
