<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioItem extends Model
{
    protected $fillable = [
        'student_id', 'title', 'description', 'media_url', 'media_type', 'is_public',
    ];

    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
