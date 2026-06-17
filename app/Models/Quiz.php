<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'course_id', 'module_id', 'title', 'description', 'max_attempts', 'is_published', 'time_limit', 'randomize_questions', 'show_results', 'grading_method',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'time_limit' => 'integer',
            'randomize_questions' => 'boolean',
            'show_results' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order_index');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function gradedAttempt($studentId = null)
    {
        $studentId = $studentId ?? auth()->id();
        $attempts = $this->attempts()->where('student_id', $studentId)->get();

        if ($attempts->isEmpty()) {
            return null;
        }

        return match ($this->grading_method) {
            'min'   => $attempts->sortBy('score')->first(),
            'last'  => $attempts->sortByDesc('created_at')->first(),
            'first' => $attempts->sortBy('created_at')->first(),
            'avg'   => $attempts->sortByDesc('created_at')->first(), // latest as representative
            default => $attempts->sortByDesc('score')->first(),
        };
    }

    public function computedGrade($studentId = null)
    {
        $studentId = $studentId ?? auth()->id();
        $attempts = $this->attempts()->where('student_id', $studentId)->get();

        if ($attempts->isEmpty()) {
            return null;
        }

        $score = match ($this->grading_method) {
            'min'   => $attempts->min('score'),
            'last'  => $attempts->sortByDesc('created_at')->first()->score,
            'first' => $attempts->sortBy('created_at')->first()->score,
            'avg'   => $attempts->avg('score'),
            default => $attempts->max('score'),
        };

        return (object) [
            'score'     => round($score, 1),
            'max_score' => $attempts->first()->max_score,
            'attempts_count' => $attempts->count(),
        ];
    }
}
