<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $query = Lesson::with('module.course.instructor');

        if ($moduleId = $request->input('module_id')) {
            $query->where('module_id', $moduleId);
        }

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $lessons = $query->orderBy('order_index')->paginate(30)->withQueryString();

        $modules = Module::with('course')->orderBy('title')->get(['id', 'title', 'course_id']);

        return view('admin.lessons.index', compact('lessons', 'modules'));
    }

    public function show(Lesson $lesson)
    {
        $lesson->load([
            'module.course.instructor',
            'progress.user',
        ]);

        $completionCount = $lesson->progress()->where('completed', true)->count();

        return view('admin.lessons.show', compact('lesson', 'completionCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module_id'   => ['required', 'exists:modules,id'],
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['nullable', 'string'],
            'video_url'   => ['nullable', 'url', 'max:2048'],
            'audio_url'   => ['nullable', 'url', 'max:2048'],
            'file_url'    => ['nullable', 'url', 'max:2048'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ]);

        if (!isset($data['order_index'])) {
            $max = Lesson::where('module_id', $data['module_id'])->max('order_index');
            $data['order_index'] = ($max ?? -1) + 1;
        }

        Lesson::create($data);

        return back()->with('success', "Lesson \"{$data['title']}\" created.");
    }

    public function update(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['nullable', 'string'],
            'video_url'   => ['nullable', 'url', 'max:2048'],
            'audio_url'   => ['nullable', 'url', 'max:2048'],
            'file_url'    => ['nullable', 'url', 'max:2048'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ]);

        $lesson->update($data);

        return back()->with('success', "Lesson \"{$data['title']}\" updated.");
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'lessons' => ['required', 'array'],
            'lessons.*' => ['required', 'array'],
            'lessons.*.id' => ['required', 'exists:lessons,id'],
            'lessons.*.order_index' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($data['lessons'] as $item) {
            Lesson::where('id', $item['id'])->update(['order_index' => $item['order_index']]);
        }

        return back()->with('success', 'Lesson order updated.');
    }

    public function destroy(Lesson $lesson)
    {
        $title = $lesson->title;
        $lesson->delete();

        return back()->with('success', "Lesson \"{$title}\" deleted.");
    }
}
