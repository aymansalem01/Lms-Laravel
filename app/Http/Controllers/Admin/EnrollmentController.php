<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'course.instructor'])
            ->latest('enrolled_at');

        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($program = $request->input('program')) {
            $query->whereHas('student', function ($q) use ($program) {
                $q->where('program', $program);
            });
        }

        if ($search = $request->input('search')) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('course', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->paginate(30)->withQueryString();

        $stats = [
            'total' => Enrollment::count(),
        ];

        $courses = Course::orderBy('title')->get(['id', 'title']);
        $programs = ['Film Production', 'Digital Media', 'Game Design', 'Audio Engineering'];

        return view('admin.enrollments.index', compact('enrollments', 'stats', 'courses', 'programs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'course_id'  => ['required', 'exists:courses,id'],
        ]);

        $enrollment = Enrollment::firstOrCreate([
            'student_id' => $data['student_id'],
            'course_id'  => $data['course_id'],
        ]);

        if (!$enrollment->wasRecentlyCreated) {
            return back()->with('error', 'Student is already enrolled in this course.');
        }

        $student = User::find($data['student_id']);
        $course = Course::find($data['course_id']);

        return back()->with('success', "{$student->name} enrolled in {$course->title}.");
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'course']);

        $studentName = $enrollment->student->name;
        $courseTitle = $enrollment->course->title;
        $enrollment->delete();

        return back()->with('success', "{$studentName} removed from {$courseTitle}.");
    }

    public function bulkEnroll(Request $request)
    {
        $data = $request->validate([
            'course_id'  => ['required', 'exists:courses,id'],
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['exists:users,id'],
        ]);

        $added = 0;
        foreach ($data['student_ids'] as $studentId) {
            Enrollment::firstOrCreate([
                'student_id' => $studentId,
                'course_id'  => $data['course_id'],
            ]);
            $added++;
        }

        $course = Course::find($data['course_id']);

        return back()->with('success', "{$added} student(s) enrolled in {$course->title}.");
    }
}
