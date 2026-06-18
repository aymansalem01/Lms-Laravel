<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function grade(Request $request, Course $course, Assignment $assignment, Submission $submission)
    {
        if ($assignment->course_id !== $course->id || $submission->assignment_id !== $assignment->id) {
            abort(404);
        }

        $data = $request->validate([
            'score'    => 'required|numeric|min:0|max:' . ($assignment->max_score ?? 100),
            'feedback' => 'nullable|string|max:5000',
        ]);

        DB::transaction(function () use ($data, $submission, $assignment) {
            Grade::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'score'       => $data['score'],
                    'feedback'    => $data['feedback'] ?? null,
                    'instructor_id' => auth()->id(),
                    'graded_at'   => now(),
                ]
            );

            $submission->update(['status' => 'graded']);
        });

        return back()->with('success', 'Grade saved for ' . ($submission->student?->name ?? 'student') . '.');
    }
}
