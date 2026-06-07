<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rubric;
use App\Models\Course;
use Illuminate\Http\Request;

class RubricController extends Controller
{
    public function index(Request $request)
    {
        $query = Rubric::with('course.instructor')
            ->withCount('assignments');

        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $rubrics = $query->latest()->paginate(30)->withQueryString();

        $courses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.rubrics.index', compact('rubrics', 'courses'));
    }

    public function show(Rubric $rubric)
    {
        $rubric->load([
            'course.instructor',
            'assignments',
        ]);

        return view('admin.rubrics.show', compact('rubric'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title'     => ['required', 'string', 'max:255'],
            'criteria'  => ['required', 'array'],
            'levels'    => ['required', 'array'],
            'cells'     => ['required', 'array'],
        ]);

        Rubric::create($data);

        return back()->with('success', "Rubric \"{$data['title']}\" created.");
    }

    public function update(Request $request, Rubric $rubric)
    {
        $data = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'criteria' => ['required', 'array'],
            'levels'   => ['required', 'array'],
            'cells'    => ['required', 'array'],
        ]);

        $rubric->update($data);

        return back()->with('success', "Rubric \"{$data['title']}\" updated.");
    }

    public function destroy(Rubric $rubric)
    {
        $title = $rubric->title;
        $rubric->delete();

        return back()->with('success', "Rubric \"{$title}\" deleted.");
    }
}
