<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::with('course.instructor')
            ->withCount('questions', 'attempts');

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($request->filled('published')) {
            $query->where('is_published', (bool) $request->input('published'));
        }

        $quizzes = $query->latest()->paginate(30)->withQueryString();

        $courses = Course::orderBy('title')->get(['id', 'title']);

        $stats = [
            'total'     => Quiz::count(),
            'published' => Quiz::where('is_published', true)->count(),
            'draft'     => Quiz::where('is_published', false)->count(),
        ];

        return view('admin.quizzes.index', compact('quizzes', 'courses', 'stats'));
    }

    public function show(Quiz $quiz)
    {
        $quiz->load([
            'course.instructor',
            'questions',
            'attempts.student',
        ]);

        $quiz->loadCount('questions', 'attempts');

        $avgScore = $quiz->attempts()
            ->where('is_draft', false)
            ->whereNotNull('score')
            ->avg('score');

        return view('admin.quizzes.show', compact('quiz', 'avgScore'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'    => ['required', 'exists:courses,id'],
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'max_attempts' => ['nullable', 'integer', 'min:1'],
            'time_limit'   => ['nullable', 'integer', 'min:1'],
            'is_published' => ['boolean'],
        ]);

        Quiz::create($data);

        return back()->with('success', "Quiz \"{$data['title']}\" created.");
    }

    public function update(Request $request, Quiz $quiz)
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'max_attempts' => ['nullable', 'integer', 'min:1'],
            'time_limit'   => ['nullable', 'integer', 'min:1'],
            'is_published' => ['boolean'],
        ]);

        $quiz->update($data);

        return back()->with('success', "Quiz \"{$data['title']}\" updated.");
    }

    public function togglePublish(Quiz $quiz)
    {
        $quiz->update(['is_published' => !$quiz->is_published]);

        $status = $quiz->is_published ? 'published' : 'unpublished';

        return back()->with('success', "Quiz \"{$quiz->title}\" {$status}.");
    }

    public function destroy(Quiz $quiz)
    {
        $title = $quiz->title;
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', "Quiz \"{$title}\" deleted.");
    }
}
