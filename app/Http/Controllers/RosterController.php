<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function index(Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin() && !$course->coInstructors()->where('instructor_id', auth()->id())->exists()) {
            abort(403);
        }

        $course->load('students', 'coInstructors', 'instructor');

        $enrolledIds = $course->students()->pluck('users.id');
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('id', $enrolledIds)
            ->orderBy('name')
            ->get();

        return view('courses.roster', compact('course', 'availableStudents'));
    }

    public function addCoInstructor(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $instructor = User::where('email', $request->email)->firstOrFail();

        if (!$instructor->isInstructor()) {
            return redirect()->back()->with('error', __('User is not an instructor.'));
        }

        if ($instructor->id === $course->instructor_id) {
            return redirect()->back()->with('error', __('The main instructor cannot be added as a co-instructor.'));
        }

        if ($course->coInstructors()->where('instructor_id', $instructor->id)->exists()) {
            return redirect()->back()->with('error', __('User is already a co-instructor.'));
        }

        $course->coInstructors()->attach($instructor->id, ['added_by' => auth()->id()]);

        return redirect()->back()->with('success', __('Co-instructor added successfully.'));
    }

    public function removeCoInstructor(Course $course, User $user)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $course->coInstructors()->detach($user->id);

        return redirect()->back()->with('success', __('Co-instructor removed successfully.'));
    }

    public function globalIndex()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $courses = Course::with('students', 'instructor')->latest()->get();
        } else {
            $taughtIds = $user->taughtCourses()->pluck('id');
            $coTaughtIds = $user->coInstructedCourses()->pluck('course_id');
            $courseIds = $taughtIds->merge($coTaughtIds)->unique();
            $courses = Course::whereIn('id', $courseIds)
                ->with('students', 'instructor')
                ->latest()
                ->get();
        }

        return view('courses.roster-index', compact('courses'));
    }
}
