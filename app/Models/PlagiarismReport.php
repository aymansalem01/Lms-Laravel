<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlagiarismReport extends Model
{
    protected $fillable = [
        'submission_id', 'overall_similarity', 'ai_probability', 'matches', 'status', 'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'overall_similarity' => 'decimal:2',
            'ai_probability' => 'decimal:2',
            'matches' => 'json',
            'checked_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
