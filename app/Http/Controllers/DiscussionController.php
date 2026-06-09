<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\User;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function index(Course $course)
    {
        $topics = $course->discussions()->with('user')->withCount('replies')->latest()->get()
            ->sortByDesc('is_pinned');
        return view('discussions.index', compact('course', 'topics'));
    }

    public function show(Course $course, Discussion $discussion)
    {
        $discussion->load('user', 'replies.user');
        $isLocked = $discussion->is_locked;
        $replies = $discussion->replies()->paginate(20);
        return view('discussions.show', compact('course', 'discussion', 'isLocked', 'replies'));
    }

    public function create(Course $course)
    {
        return view('discussions.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $data['user_id'] = auth()->id();
        $discussion = $course->discussions()->create($data);

        // Notify all enrolled students
        $course->students()->each(function ($student) use ($course, $discussion) {
            $student->notifications()->create([
                'type'    => 'discussion',
                'title'   => 'New Discussion: ' . $discussion->title,
                'message' => 'A new discussion "' . $discussion->title . '" has been posted in ' . $course->title,
                'link'    => route('courses.discussions.show', [$course, $discussion]),
            ]);
        });

        // Notify the course instructor (skip the author)
        if ($course->instructor_id !== auth()->id()) {
            $course->instructor->notifications()->create([
                'type'    => 'discussion',
                'title'   => 'New Discussion: ' . $discussion->title,
                'message' => auth()->user()->name . ' posted "' . $discussion->title . '" in ' . $course->title,
                'link'    => route('courses.discussions.show', [$course, $discussion]),
            ]);
        }

        return redirect()->route('courses.discussions.index', $course)
            ->with('success', 'Discussion topic created successfully.');
    }

    public function reply(Request $request, Course $course, Discussion $discussion)
    {
        if ($discussion->is_locked) {
            abort(403, 'This discussion is locked.');
        }

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $discussion->replies()->create([
            'user_id' => auth()->id(),
            'content' => $data['content'],
        ]);

        // Notify the discussion author (if not the replier)
        if ($discussion->user_id !== auth()->id()) {
            $discussion->user->notifications()->create([
                'type'    => 'discussion_reply',
                'title'   => 'New Reply: ' . $discussion->title,
                'message' => auth()->user()->name . ' replied to "' . $discussion->title . '" in ' . $course->title,
                'link'    => route('courses.discussions.show', [$course, $discussion]),
            ]);
        }

        // Notify the course instructor (skip the replier)
        if ($course->instructor_id !== auth()->id()) {
            $course->instructor->notifications()->create([
                'type'    => 'discussion_reply',
                'title'   => 'New Reply: ' . $discussion->title,
                'message' => auth()->user()->name . ' replied to "' . $discussion->title . '" in ' . $course->title,
                'link'    => route('courses.discussions.show', [$course, $discussion]),
            ]);
        }

        return redirect()->route('courses.discussions.show', [$course, $discussion])
            ->with('success', 'Reply posted successfully.');
    }

    public function pin(Course $course, Discussion $discussion)
    {
        $discussion->update(['is_pinned' => !$discussion->is_pinned]);
        return redirect()->route('courses.discussions.show', [$course, $discussion])
            ->with('success', $discussion->is_pinned ? 'Topic pinned.' : 'Topic unpinned.');
    }

    public function lock(Course $course, Discussion $discussion)
    {
        $discussion->update(['is_locked' => !$discussion->is_locked]);
        return redirect()->route('courses.discussions.show', [$course, $discussion])
            ->with('success', $discussion->is_locked ? 'Topic locked.' : 'Topic unlocked.');
    }
}
