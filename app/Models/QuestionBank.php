<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBank extends Model
{
    protected $fillable = [
        'name', 'user_id', 'is_visible_to_all',
    ];

    protected function casts(): array
    {
        return [
            'is_visible_to_all' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'question_bank_course');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuestionBankItem::class);
    }
}
