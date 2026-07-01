<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFlow extends Model
{
    //
    protected $fillable = [
        'chain_module_id',
        'level_no',
        'level_name',
        'order',
        'role_id',
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function chainModule()
    {
        return $this->belongsTo(ChainModule::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Get users eligible to action this level.
     */
    public function getEligibleUsers()
    {
        if ($this->user_id) {
            return User::where('id', $this->user_id)->get();
        }

        if ($this->role_id) {
            return User::role($this->role->name)->get();
        }

        return collect();
    }

    public function canBeActionedBy(User $user): bool
    {
        if ($this->user_id && $this->user_id === $user->id) {
            return true;
        }

        if ($this->role_id && $user->hasRole($this->role->name)) {
            return true;
        }

        return false;
    }
}
