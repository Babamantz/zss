<?php

namespace App\Models;

use App\Traits\FilterByTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory, FilterByTenant;

    protected $fillable = [
        'user_id',
        'title',
        'heading',
        'is_swahili',
        'unit_id',
        'division_id',
        'number_of_rows',
        'attendance_path',
    ];
    protected $casts = [
        'is_swahili' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
