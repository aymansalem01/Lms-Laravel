<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        $course->loadMissing([
            'modules.lessons',
            'modules.quizzes',
            'modules.liveSessions',
            'modules.assignments',
            'modules.moduleFiles',
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
            'description' => 'nullable|string|max:1000',
            'order_index' => 'nullable|integer',
            'module_file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,7z,png,jpg,jpeg,ppt,pptx,xls,xlsx,txt,mp4,mp3|max:20480',
        ]);

        if ($request->hasFile('module_file')) {
            $data['file_path'] = $request->file('module_file')->store('modules', 'public');
        }

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
            'description' => 'nullable|string|max:1000',
            'order_index' => 'nullable|integer',
            'module_file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,7z,png,jpg,jpeg,ppt,pptx,xls,xlsx,txt,mp4,mp3|max:20480',
        ]);

        if ($request->hasFile('module_file')) {
            if ($module->file_path) {
                Storage::disk('public')->delete($module->file_path);
            }
            $data['file_path'] = $request->file('module_file')->store('modules', 'public');
        }

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
