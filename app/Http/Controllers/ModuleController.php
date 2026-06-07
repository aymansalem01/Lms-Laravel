<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        $course->loadMissing([
            'modules.lessons',
            'modules.quizzes',
            'modules.liveSessions',
            'modules.assignments',
            'quizzes',
            'liveSessions',
            'assignments',
        ]);
        $modules = $course->modules;
        $allQuizzes = $course->quizzes;
        $allLiveSessions = $course->liveSessions;
        $allAssignments = $course->assignments;
        return view('courses.content.index', compact('course', 'modules', 'allQuizzes', 'allLiveSessions', 'allAssignments'));
    }

    public function create(Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        return view('courses.content.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'order_index' => 'nullable|integer',
        ]);

        $data['course_id'] = $course->id;
        if (!isset($data['order_index'])) {
            $data['order_index'] = ($course->modules()->max('order_index') ?? 0) + 1;
        }

        Module::create($data);

        return redirect()->route('courses.content.index', $course)->with('success', 'Module created successfully.');
    }

    public function edit(Course $course, Module $module)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        return view('courses.content.edit', compact('course', 'module'));
    }

    public function update(Request $request, Course $course, Module $module)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'order_index' => 'nullable|integer',
        ]);

        $module->update($data);

        return redirect()->route('courses.content.index', $course)->with('success', 'Module updated successfully.');
    }

    public function destroy(Course $course, Module $module)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        $module->delete();
        return redirect()->route('courses.content.index', $course)->with('success', 'Module deleted successfully.');
    }
}
