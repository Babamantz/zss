<?php

namespace App\Models;

use App\Models\ApprovalChainStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalChain extends Model
{
    //
    protected $fillable = ['name', 'module', 'is_active'];

    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalChainStep::class)->orderBy('order');
    }

    public function firstStep(): ?ApprovalChainStep
    {
        return $this->steps()->first();
    }
}
