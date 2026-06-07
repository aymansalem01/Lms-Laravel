<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveSession;
use App\Models\Course;
use Illuminate\Http\Request;

class LiveSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = LiveSession::with('course.instructor');

        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($request->input('upcoming')) {
            $query->where('scheduled_at', '>=', now());
        }

        $sessions = $query->latest('scheduled_at')->paginate(30)->withQueryString();

        $courses = Course::orderBy('title')->get(['id', 'title']);

        $stats = [
            'total'    => LiveSession::count(),
            'upcoming' => LiveSession::where('scheduled_at', '>=', now())->count(),
            'past'     => LiveSession::where('scheduled_at', '<', now())->count(),
        ];

        return view('admin.live-sessions.index', compact('sessions', 'courses', 'stats'));
    }

    public function show(LiveSession $liveSession)
    {
        $liveSession->load('course.instructor');

        return view('admin.live-sessions.show', compact('liveSession'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'    => ['required', 'exists:courses,id'],
            'title'        => ['required', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date'],
            'room_url'     => ['nullable', 'url', 'max:2048'],
            'recording_url' => ['nullable', 'url', 'max:2048'],
            'provider'     => ['nullable', 'string', 'max:100'],
        ]);

        LiveSession::create($data);

        return back()->with('success', "Live session \"{$data['title']}\" created.");
    }

    public function update(Request $request, LiveSession $liveSession)
    {
        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'scheduled_at'  => ['required', 'date'],
            'room_url'      => ['nullable', 'url', 'max:2048'],
            'recording_url' => ['nullable', 'url', 'max:2048'],
            'provider'      => ['nullable', 'string', 'max:100'],
        ]);

        $liveSession->update($data);

        return back()->with('success', "Live session \"{$data['title']}\" updated.");
    }

    public function destroy(LiveSession $liveSession)
    {
        $title = $liveSession->title;
        $liveSession->delete();

        return back()->with('success', "Live session \"{$title}\" deleted.");
    }
}
