<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        $module = $lesson->module;

        $isInstructor = $course->instructor_id === auth()->id() || auth()->user()->isAdmin();

        if (!$isInstructor) {
            if (!$course->students()->where('enrollments.student_id', auth()->id())->exists()) abort(403);
        }

        $lesson->loadMissing('module', 'topics', 'prerequisiteLesson');

        $progress = LessonProgress::where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)->first();

        if (!$progress && auth()->user()->isStudent()) {
            $progress = LessonProgress::create([
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id,
                'completed' => false,
            ]);
        }

        $isLocked = false;
        $prerequisiteLesson = null;
        if (auth()->user()->isStudent() && $lesson->prerequisite_lesson_id) {
            $prerequisiteLesson = $lesson->prerequisiteLesson;
            $prerequisiteCompleted = LessonProgress::where('user_id', auth()->id())
                ->where('lesson_id', $lesson->prerequisite_lesson_id)
                ->where('completed', true)
                ->exists();
            $isLocked = !$prerequisiteCompleted;
        }

        $prevLesson = Lesson::where('module_id', $module->id)
            ->where('order_index', '<', $lesson->order_index)
            ->orderBy('order_index', 'desc')->first();

        $nextLesson = Lesson::where('module_id', $module->id)
            ->where('order_index', '>', $lesson->order_index)
            ->orderBy('order_index')->first();

        return view('courses.content.lesson-show', compact('course', 'module', 'lesson', 'progress', 'prevLesson', 'nextLesson', 'isLocked', 'prerequisiteLesson'));
    }

    public function create(Course $course, Request $request)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        $module = Module::findOrFail($request->query('module_id', $course->modules()->first()?->id));
        abort_if(!$module || $module->course_id !== $course->id, 404);
        $course->load('modules.lessons');
        return view('courses.content.lesson-create', compact('course', 'module'));
    }

    public function store(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);

        $data = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url|max:2048',
            'audio_url' => 'nullable|url|max:2048',
            'file_url' => 'nullable|url|max:2048',
            'order_index' => 'nullable|integer',
            'prerequisite_lesson_id' => 'nullable|exists:lessons,id',
        ]);

        $module = Module::findOrFail($data['module_id']);
        if (!isset($data['order_index'])) {
            $data['order_index'] = ($module->lessons()->max('order_index') ?? 0) + 1;
        }

        Lesson::create($data);

        return redirect()->route('courses.content.index', $course)->with('success', 'Lesson created successfully.');
    }

    public function edit(Course $course, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        $course->load('modules.lessons');
        return view('courses.content.lesson-edit', compact('course', 'lesson'));
    }

    public function update(Request $request, Course $course, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url|max:2048',
            'audio_url' => 'nullable|url|max:2048',
            'file_url' => 'nullable|url|max:2048',
            'order_index' => 'nullable|integer',
            'prerequisite_lesson_id' => 'nullable|exists:lessons,id',
        ]);

        $lesson->update($data);

        return redirect()->route('courses.content.index', $course)->with('success', 'Lesson updated successfully.');
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        $lesson->delete();
        return redirect()->route('courses.content.index', $course)->with('success', 'Lesson deleted successfully.');
    }

    public function complete(Course $course, Lesson $lesson)
    {
        $progress = LessonProgress::where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)->first();

        if ($progress) {
            $progress->update(['completed' => true]);
        } else {
            LessonProgress::create([
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id,
                'completed' => true,
            ]);
        }

        return back()->with('success', __('Lesson marked as complete.'));
    }
}
