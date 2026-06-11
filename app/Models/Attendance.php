<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'languages',
        'default_lang',
        'title',
        'heading',
        'number_of_rows',
        'attendance_path',
    ];

    protected $casts = [
        'languages' => 'array',
        'title' => 'array',
        'heading' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasLanguage($lang): bool
    {
        return in_array($lang, $this->languages ?? []);
    }

    public function getColumnHeaders($lang = null)
    {
        $lang = $lang ?? $this->default_lang;

        return $lang === 'sw'
            ? ['No.', 'Jina', 'Cheo', 'Unapotoka', 'Saini']
            : ['No.', 'Name', 'Position', 'From', 'Signature'];
    }

    public function getTitle($lang = null)
    {
        $lang = $lang ?? $this->default_lang;
        return $this->title[$lang] ?? '';
    }

    public function getHeading($lang = null)
    {
        $lang = $lang ?? $this->default_lang;
        return $this->heading[$lang] ?? '';
    }
}
