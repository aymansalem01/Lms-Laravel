<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LiveSession;
use App\Services\LiveKitService;
use Illuminate\Http\Request;

class LiveSessionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $sessions = LiveSession::with('course')->orderBy('scheduled_at')->get();
        } elseif ($user->isInstructor()) {
            $courseIds = $user->taughtCourses()->pluck('id');
            $sessions = LiveSession::whereIn('course_id', $courseIds)->with('course')->orderBy('scheduled_at')->get();
        } else {
            $courseIds = $user->enrolledCourses()->pluck('courses.id');
            $sessions = LiveSession::whereIn('course_id', $courseIds)->with('course')->orderBy('scheduled_at')->get();
        }

        $upcoming = $sessions->filter(fn($s) => $s->scheduled_at && $s->scheduled_at->isFuture());
        $past = $sessions->filter(fn($s) => !$s->scheduled_at || $s->scheduled_at->isPast());

        return view('live.index', compact('upcoming', 'past'));
    }

    public function courseIndex(Course $course)
    {
        $sessions = $course->liveSessions()->with('course')->orderBy('scheduled_at')->get();
        $upcoming = $sessions->filter(fn($s) => $s->scheduled_at && $s->scheduled_at->isFuture());
        $past = $sessions->filter(fn($s) => !$s->scheduled_at || $s->scheduled_at->isPast());
        return view('live.index', compact('course', 'sessions', 'upcoming', 'past'));
    }

    public function show(Course $course, LiveSession $session)
    {
        return view('live.show', compact('course', 'session'));
    }

    public function showStandalone(LiveSession $session)
    {
        return view('live.show', compact('session'));
    }

    public function create(Course $course)
    {
        $courses = auth()->user()->taughtCourses()->get();
        $modules = $course->modules ?? collect();
        return view('live.create', compact('course', 'courses', 'modules'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'scheduled_at'  => 'nullable|date',
            'room_url'      => 'nullable|url|max:2048',
            'recording_url' => 'nullable|url|max:2048',
            'provider'      => 'nullable|string|in:livekit,whereby,external',
            'mode'          => 'nullable|string|in:builtin,external',
            'duration'      => 'nullable|integer|min:1|max:1440',
            'module_id'     => 'nullable|exists:modules,id',
        ]);

        $data['provider'] ??= 'whereby';
        $data['module_id'] = $request->filled('module_id') ? $data['module_id'] : null;

        if ($data['provider'] === 'livekit') {
            $data['room_url'] = 'room-' . strtolower(str_replace(' ', '-', $data['title'])) . '-' . uniqid();
        } elseif (empty($data['room_url'])) {
            $data['room_url'] = 'https://whereby.com/' . strtolower(str_replace(' ', '-', $data['title'])) . '-' . uniqid();
        }

        if ($data['provider'] === 'livekit') {
            try {
                app(LiveKitService::class)->createRoom($data['room_url']);
            } catch (\Throwable $e) {
                logger()->warning('Failed to create LiveKit room: ' . $e->getMessage());
            }
        }

        $course->liveSessions()->create($data);

        $course->students()->each(function ($student) use ($course, $data) {
            $student->notifications()->create([
                'type'    => 'live_session',
                'title'   => 'New Live Session: ' . $data['title'],
                'message' => 'A new live session "' . $data['title'] . '" has been scheduled for ' . $course->title,
                'link'    => route('courses.live.index', $course),
            ]);
        });

        return redirect()->route('courses.live.index', $course)->with('success', 'Live session created successfully.');
    }

    public function storeStandalone(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'course_id'     => 'required|integer|exists:courses,id',
            'scheduled_at'  => 'nullable|date',
            'room_url'      => 'nullable|url|max:2048',
            'recording_url' => 'nullable|url|max:2048',
            'provider'      => 'nullable|string|in:livekit,whereby,external',
            'mode'          => 'nullable|string|in:builtin,external',
            'duration'      => 'nullable|integer|min:1|max:1440',
        ]);

        $data['provider'] ??= 'whereby';

        if ($data['provider'] === 'livekit') {
            $data['room_url'] = 'room-' . strtolower(str_replace(' ', '-', $data['title'])) . '-' . uniqid();
            try {
                app(LiveKitService::class)->createRoom($data['room_url']);
            } catch (\Throwable $e) {
                logger()->warning('Failed to create LiveKit room: ' . $e->getMessage());
            }
        } elseif (empty($data['room_url'])) {
            $data['room_url'] = 'https://whereby.com/' . strtolower(str_replace(' ', '-', $data['title'])) . '-' . uniqid();
        }

        LiveSession::create($data);

        $course = Course::findOrFail($data['course_id']);
        $course->students()->each(function ($student) use ($course, $data) {
            $student->notifications()->create([
                'type'    => 'live_session',
                'title'   => 'New Live Session: ' . $data['title'],
                'message' => 'A new live session "' . $data['title'] . '" has been scheduled for ' . $course->title,
                'link'    => route('courses.live.index', $course),
            ]);
        });

        return redirect()->route('live.index')->with('success', 'Live session created successfully.');
    }

    public function editStandalone(LiveSession $session)
    {
        $courses = auth()->user()->isAdmin()
            ? Course::all()
            : auth()->user()->taughtCourses()->get();
        return view('live.edit', compact('session', 'courses'));
    }

    public function updateStandalone(Request $request, LiveSession $session)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'course_id'     => 'required|integer|exists:courses,id',
            'scheduled_at'  => 'nullable|date',
            'room_url'      => 'nullable|url|max:2048',
            'recording_url' => 'nullable|url|max:2048',
            'provider'      => 'nullable|string|in:livekit,whereby,external',
            'mode'          => 'nullable|string|in:builtin,external',
            'duration'      => 'nullable|integer|min:1|max:1440',
        ]);

        $session->update($data);

        return redirect()->route('live.index')->with('success', 'Live session updated successfully.');
    }

    public function destroy(LiveSession $session)
    {
        $session->delete();
        return redirect()->route('live.index')->with('success', 'Live session deleted successfully.');
    }
}
