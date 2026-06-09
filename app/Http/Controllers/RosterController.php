<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function index(Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $course->load('students', 'instructor');

        $enrolledIds = $course->students()->pluck('users.id');
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('id', $enrolledIds)
            ->orderBy('name')
            ->get();

        return view('courses.roster', compact('course', 'availableStudents'));
    }

    public function globalIndex()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $courses = Course::with('students', 'instructor')->latest()->get();
        } else {
            $courseIds = $user->taughtCourses()->pluck('id');
            $courses = Course::whereIn('id', $courseIds)
                ->with('students', 'instructor')
                ->latest()
                ->get();
        }

        return view('courses.roster-index', compact('courses'));
    }
}
