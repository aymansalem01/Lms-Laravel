<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\QuestionBank;
use App\Models\QuestionBankItem;

class Course extends Model
{
    protected $fillable = [
        'title', 'description', 'program', 'course_type', 'instructor_id', 'cover_image_url', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order_index');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function rubrics(): HasMany
    {
        return $this->hasMany(Rubric::class);
    }

    public function liveSessions(): HasMany
    {
        return $this->hasMany(LiveSession::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'course_programs')->withPivot('course_type');
    }

    public function files(): HasMany
    {
        return $this->hasMany(CourseFile::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(CourseAttendance::class);
    }

    public function attendanceWarnings(): HasMany
    {
        return $this->hasMany(AttendanceWarning::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function questionBanks(): BelongsToMany
    {
        return $this->belongsToMany(QuestionBank::class, 'question_bank_course');
    }

    public function questionBankItems()
    {
        return QuestionBankItem::whereIn('question_bank_id', $this->questionBanks()->select('question_banks.id'));
    }

    public function gradeRules(): HasMany
    {
        return $this->hasMany(GradeRule::class);
    }

    public function getNameAttribute(): string
    {
        return $this->title;
    }
}
