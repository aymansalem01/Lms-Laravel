<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id', 'student_id', 'answers', 'score', 'max_score', 'submitted_at',
        'is_draft', 'released_at',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'json',
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'submitted_at' => 'datetime',
            'is_draft' => 'boolean',
            'released_at' => 'datetime',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
