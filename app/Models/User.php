<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Modules\HRM\Models\Employee;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'is_active',
        'is_officer',
        'created_by',
        'updated_by',
        'tenant_id',
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_officer' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
    public function employee()
    {
        return  $this->hasOne(Employee::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }



    // // Override boot to add tenant scoping
    // protected static function booted()
    // {
    //     static::addGlobalScope('tenant', function ($builder) {
    //         if ($tenantId = app('tenant.id')) {
    //             $builder->where('tenant_id', $tenantId);
    //         }
    //     });
    // }
}
