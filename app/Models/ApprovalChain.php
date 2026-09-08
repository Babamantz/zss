<?php

namespace App\Models;

use App\Models\ApprovalChainStep;
use App\Traits\Approval\FilterByTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalChain extends Model
{
    //
    use FilterByTenant;
    protected $fillable = ['name', 'module', 'is_active','tenant_id'];

    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalChainStep::class)->orderBy('order');
    }

    public function firstStep(): ?ApprovalChainStep
    {
        return $this->steps()->first();
    }
}
