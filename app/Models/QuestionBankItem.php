<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBankItem extends Model
{
    protected $fillable = [
        'course_id', 'user_id', 'type', 'question', 'options', 'correct_answer', 'points',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'json',
            'points' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(QuizQuestion::class, 'bank_item_id');
    }
}
