<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Assignment::with('course.instructor')
            ->withCount('submissions');

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($request->filled('has_due_date')) {
            $query->whereNotNull('due_date');
        }

        $assignments = $query->latest()->paginate(30)->withQueryString();

        $courses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.assignments.index', compact('assignments', 'courses'));
    }

    public function show(Assignment $assignment)
    {
        $assignment->load([
            'course.instructor',
            'submissions.student',
            'submissions.grade',
            'rubric',
        ]);

        $submissionCount = $assignment->submissions()->count();
        $gradedCount = $assignment->submissions()->where('status', 'graded')->count();

        return view('admin.assignments.show', compact('assignment', 'submissionCount', 'gradedCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title'     => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date'  => ['nullable', 'date'],
            'max_score' => ['nullable', 'integer', 'min:1'],
            'rubric_ref' => ['nullable', 'exists:rubrics,id'],
        ]);

        Assignment::create($data);

        return back()->with('success', "Assignment \"{$data['title']}\" created.");
    }

    public function update(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date'    => ['nullable', 'date'],
            'max_score'   => ['nullable', 'integer', 'min:1'],
            'rubric_ref'  => ['nullable', 'exists:rubrics,id'],
        ]);

        $assignment->update($data);

        return back()->with('success', "Assignment \"{$data['title']}\" updated.");
    }

    public function destroy(Assignment $assignment)
    {
        $title = $assignment->title;
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', "Assignment \"{$title}\" deleted.");
    }
}
