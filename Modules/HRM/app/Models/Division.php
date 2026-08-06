<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\HRM\Database\Factories\DivisionFactory;

class Division extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): DivisionFactory
    // {
    //     // return DivisionFactory::new();
    // }
}
