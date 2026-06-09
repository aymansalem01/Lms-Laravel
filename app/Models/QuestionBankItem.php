<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBankItem extends Model
{
    protected $fillable = [
        'question_bank_id', 'user_id', 'type', 'question', 'options', 'correct_answer', 'points',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'json',
            'points' => 'integer',
        ];
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
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
