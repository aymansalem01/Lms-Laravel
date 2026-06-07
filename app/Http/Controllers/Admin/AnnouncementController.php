<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with(['author', 'course'])
            ->withCount('dismissals');

        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $announcements = $query->latest()->paginate(30)->withQueryString();

        $courses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.announcements.index', compact('announcements', 'courses'));
    }

    public function show(Announcement $announcement)
    {
        $announcement->load([
            'author',
            'course.instructor',
            'dismissals.user',
        ]);

        return view('admin.announcements.show', compact('announcement'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['nullable', 'exists:courses,id'],
            'title'     => ['required', 'string', 'max:255'],
            'content'   => ['required', 'string'],
            'priority'  => ['nullable', Rule::in(['low', 'normal', 'high', 'urgent'])],
        ]);

        Announcement::create([
            'author_id' => Auth::id(),
            'course_id' => $data['course_id'],
            'title'     => $data['title'],
            'content'   => $data['content'],
            'priority'  => $data['priority'] ?? 'normal',
        ]);

        return back()->with('success', 'Announcement published.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'content'  => ['required', 'string'],
            'priority' => ['nullable', Rule::in(['low', 'normal', 'high', 'urgent'])],
        ]);

        $announcement->update($data);

        return back()->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }
}
