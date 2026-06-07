<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Program extends Model
{
    protected $fillable = ['name', 'description'];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_programs')->withPivot('course_type');
    }
}
