<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id', 'student_id', 'file_url', 'video_url', 'audio_url',
        'link_url', 'file_path', 'notes', 'status', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class);
    }

    public function plagiarismReport(): HasOne
    {
        return $this->hasOne(PlagiarismReport::class, 'submission_id');
    }

    public function getLinkAttribute(): ?string
    {
        return $this->link_url;
    }
}
