<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index(Course $course)
    {
        $assignments = $course->assignments()->withCount('submissions')->get();
        return view('assignments.index', compact('course', 'assignments'));
    }

    public function show(Course $course, Assignment $assignment)
    {
        $user = auth()->user();

        if ($user->isStudent()) {
            $submission = $assignment->submissions()->where('student_id', $user->id)->first();
            return view('assignments.show', compact('course', 'assignment', 'submission'));
        }

        $submissions = $assignment->submissions()->with('student', 'grade')->get();
        return view('assignments.show', compact('course', 'assignment', 'submissions'));
    }

    public function create(Course $course)
    {
        if (!auth()->user()->isInstructorOrAdmin()) {
            abort(403);
        }
        $rubrics = $course->rubrics()
            ->when(!auth()->user()->isAdmin(), fn($q) => $q->where('instructor_id', auth()->id()))
            ->get();
        $modules = $course->modules ?? collect();
        return view('assignments.create', compact('course', 'rubrics', 'modules'));
    }

    public function store(Request $request, Course $course)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'max_score'   => 'nullable|integer|min:0',
            'rubric_ref'  => 'nullable|exists:rubrics,id',
            'module_id'   => 'nullable|exists:modules,id',
            'attachment'  => 'nullable|file|mimes:pdf,doc,docx,zip,rar,7z,png,jpg,jpeg,ppt,pptx,xls,xlsx,txt|max:10240',
        ]);

        if ($request->hasFile('attachment')) {
            $data['file_path'] = $request->file('attachment')->store('assignments', 'public');
        }

        $data['rubric'] = $request->input('rubric');
        $data['module_id'] = $request->filled('module_id') ? $data['module_id'] : null;
        $assignment = $course->assignments()->create($data);

        $course->students()->each(function ($student) use ($course, $assignment) {
            $student->notifications()->create([
                'type'    => 'assignment',
                'title'   => 'New Assignment: ' . $assignment->title,
                'message' => 'A new assignment "' . $assignment->title . '" has been created in ' . $course->title,
                'link'    => route('courses.assignments.show', [$course, $assignment]),
            ]);
        });

        return redirect()->route('courses.assignments.index', $course)
            ->with('success', 'Assignment created successfully.');
    }

    public function edit(Course $course, Assignment $assignment)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $rubrics = $course->rubrics()
            ->when(!auth()->user()->isAdmin(), fn($q) => $q->where('instructor_id', auth()->id()))
            ->get();
        $modules = $course->modules ?? collect();
        return view('assignments.edit', compact('course', 'assignment', 'rubrics', 'modules'));
    }

    public function update(Request $request, Course $course, Assignment $assignment)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'max_score'   => 'nullable|integer|min:0',
            'rubric_ref'  => 'nullable|exists:rubrics,id',
            'module_id'   => 'nullable|exists:modules,id',
            'attachment'  => 'nullable|file|mimes:pdf,doc,docx,zip,rar,7z,png,jpg,jpeg,ppt,pptx,xls,xlsx,txt|max:10240',
        ]);

        if ($request->hasFile('attachment')) {
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            $data['file_path'] = $request->file('attachment')->store('assignments', 'public');
        }

        $data['rubric'] = $request->input('rubric');
        $data['module_id'] = $request->filled('module_id') ? $data['module_id'] : null;
        $assignment->update($data);

        return redirect()->route('courses.assignments.index', $course)
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Course $course, Assignment $assignment)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $assignment->delete();

        return redirect()->route('courses.assignments.index', $course)
            ->with('success', 'Assignment deleted successfully.');
    }

    public function gradebook(Course $course, Assignment $assignment)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $students = $course->students()->with(['submissions' => function ($q) use ($assignment) {
            $q->where('assignment_id', $assignment->id);
        }, 'submissions.grade'])->get();

        return view('assignments.gradebook', compact('course', 'assignment', 'students'));
    }

    public function updateGrade(Request $request, Course $course, Assignment $assignment, Submission $submission)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'score'    => 'required|numeric|min:0|max:' . ($assignment->max_score ?? 100),
            'feedback' => 'nullable|string|max:2000',
        ]);

        $data['instructor_id'] = auth()->id();
        $data['graded_at'] = now();

        $submission->grade()->updateOrCreate(
            ['submission_id' => $submission->id],
            $data
        );

        return response()->json(['success' => true]);
    }

    public function studentIndex()
    {
        $user = auth()->user();

        if ($user->isInstructorOrAdmin()) {
            $courseIds = $user->isAdmin()
                ? Course::pluck('id')
                : $user->taughtCourses()->pluck('id');

            $assignments = Assignment::whereIn('course_id', $courseIds)
                ->with('course', 'submissions.student', 'submissions.grade')
                ->withCount('submissions')
                ->get()
                ->groupBy(fn($a) => $a->course->title);

            return view('assignments.instructor-index', compact('assignments'));
        }

        $submissions = $user->submissions()->with('assignment.course')->get();
        $submittedAssignmentIds = $submissions->pluck('assignment_id');

        $enrolledCourseIds = $user->enrolledCourses()->pluck('courses.id');

        $assignments = Assignment::whereIn('course_id', $enrolledCourseIds)
            ->with('course', 'submissions')
            ->get();

        return view('assignments.student-index', compact('assignments'));
    }
}
