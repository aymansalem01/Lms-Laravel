<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index(Lesson $lesson)
    {
        return redirect()->route('courses.content.lesson.show', [$lesson->module->course, $lesson]);
    }

    public function create(Course $course, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        abort_if($lesson->module->course_id !== $course->id, 404);
        return view('courses.content.topics.create', compact('course', 'lesson'));
    }

    public function store(Request $request, Course $course, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        abort_if($lesson->module->course_id !== $course->id, 404);

        $data = $request->validate([
            'type' => 'required|in:file,link,html,video,audio',
            'title' => 'required|string|max:255',
            'file_url' => 'nullable|url|max:2048',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url|max:2048',
            'audio_url' => 'nullable|url|max:2048',
            'external_url' => 'nullable|url|max:2048',
        ]);

        $data['lesson_id'] = $lesson->id;
        $data['order_index'] = $request->input('order_index', ($lesson->topics()->max('order_index') ?? 0) + 1);

        Topic::create($data);

        return redirect()->route('courses.content.lesson.show', [$course, $lesson])
            ->with('success', __('Topic created successfully.'));
    }

    public function edit(Course $course, Lesson $lesson, Topic $topic)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        abort_if($topic->lesson_id !== $lesson->id || $lesson->module->course_id !== $course->id, 404);
        return view('courses.content.topics.edit', compact('course', 'lesson', 'topic'));
    }

    public function update(Request $request, Course $course, Lesson $lesson, Topic $topic)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        abort_if($topic->lesson_id !== $lesson->id || $lesson->module->course_id !== $course->id, 404);

        $data = $request->validate([
            'type' => 'required|in:file,link,html,video,audio',
            'title' => 'required|string|max:255',
            'file_url' => 'nullable|url|max:2048',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url|max:2048',
            'audio_url' => 'nullable|url|max:2048',
            'external_url' => 'nullable|url|max:2048',
        ]);

        $topic->update($data);

        return redirect()->route('courses.content.lesson.show', [$course, $lesson])
            ->with('success', __('Topic updated successfully.'));
    }

    public function destroy(Course $course, Lesson $lesson, Topic $topic)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) abort(403);
        abort_if($topic->lesson_id !== $lesson->id || $lesson->module->course_id !== $course->id, 404);

        $topic->delete();

        return redirect()->route('courses.content.lesson.show', [$course, $lesson])
            ->with('success', __('Topic deleted successfully.'));
    }
}
