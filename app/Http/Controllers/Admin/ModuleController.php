<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        $query = Module::with('course.instructor')
            ->withCount('lessons');

        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $modules = $query->orderBy('order_index')->paginate(30)->withQueryString();

        $courses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.modules.index', compact('modules', 'courses'));
    }

    public function show(Module $module)
    {
        $module->load([
            'course.instructor',
            'lessons',
        ]);

        return view('admin.modules.show', compact('module'));
    }

    public function create()
    {
        $courses = Course::orderBy('title')->get(['id', 'title']);
        return view('admin.modules.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'   => ['required', 'exists:courses,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'module_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar,7z,png,jpg,jpeg,ppt,pptx,xls,xlsx,txt,mp4,mp3', 'max:20480'],
        ]);

        if ($request->hasFile('module_file')) {
            $data['file_path'] = $request->file('module_file')->store('modules', 'public');
        }

        if (!isset($data['order_index'])) {
            $max = Module::where('course_id', $data['course_id'])->max('order_index');
            $data['order_index'] = ($max ?? -1) + 1;
        }

        Module::create($data);

        return redirect()->route('admin.modules.index')->with('success', "Module \"{$data['title']}\" created.");
    }

    public function edit(Module $module)
    {
        $module->load('course');
        return view('admin.modules.edit', compact('module'));
    }

    public function update(Request $request, Module $module)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'module_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar,7z,png,jpg,jpeg,ppt,pptx,xls,xlsx,txt,mp4,mp3', 'max:20480'],
        ]);

        if ($request->hasFile('module_file')) {
            if ($module->file_path) {
                Storage::disk('public')->delete($module->file_path);
            }
            $data['file_path'] = $request->file('module_file')->store('modules', 'public');
        } elseif ($request->boolean('remove_module_file') && $module->file_path) {
            Storage::disk('public')->delete($module->file_path);
            $data['file_path'] = null;
        }

        $module->update($data);

        return redirect()->route('admin.modules.index')->with('success', "Module \"{$data['title']}\" updated.");
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'modules' => ['required', 'array'],
            'modules.*' => ['required', 'array'],
            'modules.*.id' => ['required', 'exists:modules,id'],
            'modules.*.order_index' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($data['modules'] as $item) {
            Module::where('id', $item['id'])->update(['order_index' => $item['order_index']]);
        }

        return back()->with('success', 'Module order updated.');
    }

    public function destroy(Module $module)
    {
        $title = $module->title;
        $module->delete();

        return back()->with('success', "Module \"{$title}\" deleted.");
    }
}
