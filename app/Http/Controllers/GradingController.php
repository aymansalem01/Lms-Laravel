<?php

namespace App\Http\Controllers;

use App\Mail\GradeReleased;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class GradingController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $user = auth()->user();
        $submissions = Submission::whereHas('assignment.course', function ($q) use ($user) {
                if ($user->isInstructor()) {
                    $q->where('instructor_id', $user->id);
                }
            })
            ->with('assignment.course', 'student', 'grade')
            ->latest()
            ->get();

        return view('grading.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $submission->load('assignment.course', 'student', 'grade');

        return view('grading.show', compact('submission'));
    }

    public function store(Request $request, Submission $submission)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'score'    => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $data['instructor_id'] = auth()->id();
        $data['graded_at'] = now();

        $submission->grade()->updateOrCreate(
            ['submission_id' => $submission->id],
            $data
        );

        if ($request->input('action') === 'publish') {
            $submission->load('student', 'assignment.course');
            Mail::to($submission->student->email)->send(new GradeReleased(
                studentName: $submission->student->name,
                courseTitle: $submission->assignment->course->title,
                assignmentTitle: $submission->assignment->title,
                score: $data['score'],
                feedback: $data['feedback'] ?? null,
                courseId: $submission->assignment->course_id,
            ));
            $submission->student->notifications()->create([
                'type'    => 'grade_released',
                'title'   => 'Grade Published: ' . $submission->assignment->title,
                'message' => 'Your grade for "' . $submission->assignment->title . '" in ' . $submission->assignment->course->title . ' has been published. Score: ' . $data['score'] . '/100',
                'link'    => route('courses.assignments.show', [$submission->assignment->course_id, $submission->assignment_id]),
            ]);
        }

        return redirect()->route('grading.index')
            ->with('success', 'Grade saved successfully.');
    }

    public function release(Submission $submission)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $submission->grade()->update(['released_at' => now()]);

        $submission->load('student', 'assignment.course', 'grade');
        Mail::to($submission->student->email)->send(new GradeReleased(
            studentName: $submission->student->name,
            courseTitle: $submission->assignment->course->title,
            assignmentTitle: $submission->assignment->title,
            score: $submission->grade->score,
            feedback: $submission->grade->feedback,
            courseId: $submission->assignment->course_id,
        ));
        $submission->student->notifications()->create([
            'type'    => 'grade_released',
            'title'   => 'Grade Released: ' . $submission->assignment->title,
            'message' => 'Your grade for "' . $submission->assignment->title . '" in ' . $submission->assignment->course->title . ' has been released. Score: ' . $submission->grade->score . '/100',
            'link'    => route('courses.assignments.show', [$submission->assignment->course_id, $submission->assignment_id]),
        ]);

        return redirect()->route('grading.index')
            ->with('success', 'Grade released successfully.');
    }

    public function releaseAll(Assignment $assignment)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $assignment->submissions()->whereHas('grade', function ($q) {
            $q->whereNull('released_at');
        })->each(function ($submission) {
            $submission->grade()->update(['released_at' => now()]);
        });

        return redirect()->route('assignments.show', [$assignment->course_id, $assignment])
            ->with('success', 'All grades released successfully.');
    }

    public function gradebook(Course $course, Assignment $assignment)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $students = $course->students()
            ->with(['submissions' => function ($q) use ($assignment) {
                $q->where('assignment_id', $assignment->id);
            }, 'submissions.grade'])
            ->get();

        $totalSubmissions = $students->filter(fn($s) => $s->submissions->isNotEmpty())->count();
        $gradedCount = $students->filter(fn($s) => $s->submissions->first()?->grade)->count();
        $averageScore = $students->filter(fn($s) => $s->submissions->first()?->grade)
            ->avg(fn($s) => $s->submissions->first()->grade->score);

        return view('assignments.gradebook', compact('course', 'assignment', 'students', 'totalSubmissions', 'gradedCount', 'averageScore'));
    }

    public function directGrade(Request $request, Course $course, Assignment $assignment, User $student)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'score'    => 'required|numeric|min:0|max:' . ($assignment->max_score ?? 100),
            'feedback' => 'nullable|string|max:2000',
        ]);

        $submission = $assignment->submissions()->firstOrCreate(
            ['student_id' => $student->id],
            ['status' => 'graded', 'submitted_at' => now()]
        );

        $submission->grade()->updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'score'         => $data['score'],
                'feedback'      => $data['feedback'] ?? null,
                'instructor_id' => auth()->id(),
                'graded_at'     => now(),
            ]
        );

        return response()->json(['success' => true]);
    }
}
