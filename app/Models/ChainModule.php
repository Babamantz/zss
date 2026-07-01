<?php

namespace App\Models;

use App\Models\ChainTransaction;
use App\Models\CustomerFlow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChainModule extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'module_code',
        'total_levels',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function steps()
    {
        return $this->hasMany(
            ApprovalStep::class
        )->orderBy('order');
    }

    public function approvalRequests()
    {
        return $this->hasMany(
            ApprovalRequest::class
        );
    }

 

    // ── Relationships ─────────────────────────────────────────────────────────

    // public function flows()
    // {
    //     return $this->hasMany(CustomerFlow::class)->orderBy('order');
    // }

    // public function activeFlows()
    // {
    //     return $this->flows()->where('is_active', true);
    // }

    // public function transactions()
    // {
    //     return $this->hasMany(ChainTransaction::class);
    // }

    // // ── Helpers ───────────────────────────────────────────────────────────────

    // public static function findByCode(string $code): ?self
    // {
    //     return static::where('code', $code)
    //         ->where('is_active', true)
    //         ->first();
    // }

    // public function getLevelCount(): int
    // {
    //     return $this->activeFlows()->count();
    // }
}
