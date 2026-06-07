<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id', 'type', 'question', 'options', 'correct_answer', 'points', 'order_index', 'bank_item_id',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'json',
            'points' => 'integer',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function bankItem(): BelongsTo
    {
        return $this->belongsTo(QuestionBankItem::class, 'bank_item_id');
    }
}
