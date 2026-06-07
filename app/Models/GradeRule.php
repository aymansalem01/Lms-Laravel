<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeRule extends Model
{
    protected $fillable = [
        'course_id', 'category', 'weight',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
