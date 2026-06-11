<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Illuminate\Http\Request;

class GradingController extends Controller
{
    public function index()
    {
        $courses = Course::withCount([
            'assignments',
            'assignments as total_submissions_count' => function ($q) {
                $q->whereHas('submissions');
            },
            'assignments as graded_submissions_count' => function ($q) {
                $q->whereHas('submissions', fn($sq) => $sq->where('status', 'graded'));
            },
        ])->orderBy('title')->paginate(20);

        return view('admin.grading.index', compact('courses'));
    }

    public function assignments(Course $course)
    {
        $assignments = $course->assignments()
            ->withCount([
                'submissions',
                'submissions as graded_count' => fn($q) => $q->where('status', 'graded'),
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.grading.assignments', compact('course', 'assignments'));
    }

    public function submissions(Course $course, Assignment $assignment, Request $request)
    {
        if ($assignment->course_id !== $course->id) {
            abort(404);
        }

        $query = $assignment->submissions()
            ->with(['student', 'grade.instructor'])
            ->latest('submitted_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $submissions = $query->paginate(30)->withQueryString();

        $stats = [
            'total'  => $assignment->submissions()->count(),
            'graded' => $assignment->submissions()->where('status', 'graded')->count(),
            'pending' => $assignment->submissions()->where('status', 'submitted')->count(),
            'late'   => $assignment->submissions()->where('status', 'late')->count(),
        ];

        return view('admin.grading.submissions', compact('course', 'assignment', 'submissions', 'stats'));
    }
}
