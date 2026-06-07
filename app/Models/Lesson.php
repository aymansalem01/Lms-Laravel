<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = [
        'module_id', 'title', 'content', 'video_url', 'audio_url', 'file_url', 'order_index',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class)->orderBy('order_index');
    }

    public function prerequisiteLesson()
    {
        return $this->belongsTo(Lesson::class, 'prerequisite_lesson_id');
    }
}
