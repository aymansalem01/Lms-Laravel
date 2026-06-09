<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rubric extends Model
{
    protected $fillable = ['course_id', 'instructor_id', 'title', 'criteria', 'levels', 'cells'];

    protected function casts(): array
    {
        return [
            'criteria' => 'json',
            'levels' => 'json',
            'cells' => 'json',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'rubric_ref');
    }
}
