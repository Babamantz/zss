<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\HRM\Database\Factories\BankFactory;

class Bank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = false;

    // protected static function newFactory(): BankFactory
    // {
    //     // return BankFactory::new();
    // }
}
