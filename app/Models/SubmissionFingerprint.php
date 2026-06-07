<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFingerprint extends Model
{
    protected $fillable = ['submission_id', 'fingerprints', 'word_count'];

    protected function casts(): array
    {
        return [
            'fingerprints' => 'json',
            'word_count' => 'integer',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
