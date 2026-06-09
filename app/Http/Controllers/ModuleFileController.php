<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\ModuleFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleFileController extends Controller
{
    public function index(Course $course, Module $module)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $files = $module->moduleFiles()->orderBy('order_index')->get();
        return view('courses.files.module-index', compact('course', 'module', 'files'));
    }

    public function store(Request $request, Course $course, Module $module)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'file'       => ['required', 'file', 'mimes:pdf,doc,docx,zip,rar,7z,png,jpg,jpeg,ppt,pptx,xls,xlsx,txt,mp4,mp3', 'max:20480'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['file_path'] = $request->file('file')->store('module-files', 'public');
        $data['course_id'] = $course->id;
        $data['module_id'] = $module->id;

        if (!isset($data['order_index'])) {
            $max = $module->moduleFiles()->max('order_index');
            $data['order_index'] = ($max ?? -1) + 1;
        }

        ModuleFile::create($data);

        return redirect()->route('courses.content.index', $course)
            ->with('success', "File \"{$data['title']}\" added to module.");
    }

    public function destroy(Course $course, Module $module, ModuleFile $file)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        if ($file->file_path) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return redirect()->route('courses.content.index', $course)
            ->with('success', 'File removed.');
    }
}
