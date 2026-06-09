<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\QuestionBank;
use App\Models\QuestionBankItem;
use App\Models\User;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    public function globalIndex(Request $request)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $query = QuestionBank::with(['user', 'courses', 'items']);

        if (!auth()->user()->isAdmin()) {
            $courseIds = auth()->user()->taughtCourses()->pluck('courses.id');
            $query->where(function ($q) use ($courseIds) {
                $q->whereHas('courses', fn($q) => $q->whereIn('courses.id', $courseIds))
                  ->orWhere('is_visible_to_all', true);
            });
        }

        if ($request->filled('course_id')) {
            $query->whereHas('courses', fn($q) => $q->where('courses.id', $request->course_id));
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $banks = $query->latest()->paginate(20);
        $courses = auth()->user()->isAdmin()
            ? Course::orderBy('title')->get(['id', 'title'])
            : auth()->user()->taughtCourses()->orderBy('title')->get(['id', 'title']);

        return view('question-bank.global', compact('banks', 'courses'));
    }

    public function addItem(Request $request, QuestionBank $questionBank)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        if (!auth()->user()->isAdmin()) {
            $courseIds = auth()->user()->taughtCourses()->pluck('courses.id');
            $hasAccess = $questionBank->is_visible_to_all
                || $questionBank->courses()->whereIn('courses.id', $courseIds)->exists();
            abort_unless($hasAccess, 403);
        }

        $data = $request->validate([
            'type'             => 'required|in:multiple_choice,true_false,short_answer,long_answer',
            'question'         => 'required|string',
            'options'          => 'nullable|array',
            'options.*'        => 'nullable|string',
            'correct_answer'   => 'nullable|string',
            'points'           => 'required|integer|min:1',
        ]);

        $questionBank->items()->create([
            'user_id'        => auth()->id(),
            'type'           => $data['type'],
            'question'       => $data['question'],
            'options'        => $data['type'] === 'multiple_choice' ? array_values(array_filter($data['options'] ?? [])) : null,
            'correct_answer' => $data['correct_answer'],
            'points'         => $data['points'],
        ]);

        return redirect()->route('question-bank.index')
            ->with('success', 'Question added to "' . $questionBank->name . '".');
    }

    public function index(Course $course)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $banks = $course->questionBanks()->with('items', 'items.user', 'user')->latest()->get();
        return view('question-bank.index', compact('course', 'banks'));
    }

    public function create(Course $course)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        return view('question-bank.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'name'                     => 'required|string|max:255',
            'questions'                => 'required|array|min:1',
            'questions.*.type'         => 'required|in:multiple_choice,true_false,short_answer,long_answer',
            'questions.*.question'     => 'required|string',
            'questions.*.options'      => 'nullable|array',
            'questions.*.options.*'    => 'nullable|string',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.points'       => 'required|integer|min:1',
        ]);

        $bank = QuestionBank::create([
            'name'              => $data['name'],
            'user_id'           => auth()->id(),
            'is_visible_to_all' => false,
        ]);

        $bank->courses()->attach($course->id);

        foreach ($data['questions'] as $q) {
            $bank->items()->create([
                'user_id'        => auth()->id(),
                'type'           => $q['type'],
                'question'       => $q['question'],
                'options'        => $q['type'] === 'multiple_choice' ? array_values(array_filter($q['options'] ?? [])) : null,
                'correct_answer' => $q['correct_answer'],
                'points'         => $q['points'],
            ]);
        }

        $count = count($data['questions']);
        $redirect = $request->input('_redirect', route('courses.question-bank.index', $course));
        return redirect($redirect)->with('success', "Bank \"{$bank->name}\" created with $count questions.");
    }

    public function edit(Course $course, QuestionBankItem $questionBankItem)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $bank = $questionBankItem->bank;
        abort_if(!$course->questionBanks()->where('question_banks.id', $bank->id)->exists(), 404);
        return view('question-bank.edit', compact('course', 'questionBankItem'));
    }

    public function update(Request $request, Course $course, QuestionBankItem $questionBankItem)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $bank = $questionBankItem->bank;
        abort_if(!$course->questionBanks()->where('question_banks.id', $bank->id)->exists(), 404);

        $data = $request->validate([
            'type'             => 'required|in:multiple_choice,true_false,short_answer,long_answer',
            'question'         => 'required|string',
            'options'          => 'nullable|array',
            'options.*'        => 'nullable|string',
            'correct_answer'   => 'nullable|string',
            'points'           => 'required|integer|min:1',
        ]);

        $questionBankItem->update([
            'type'             => $data['type'],
            'question'         => $data['question'],
            'options'          => $data['type'] === 'multiple_choice' ? array_values(array_filter($data['options'] ?? [])) : null,
            'correct_answer'   => $data['correct_answer'],
            'points'           => $data['points'],
        ]);

        $redirect = $request->input('_redirect', route('courses.question-bank.index', $course));
        return redirect($redirect)->with('success', 'Question updated.');
    }

    public function destroy(Course $course, QuestionBankItem $questionBankItem)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $bank = $questionBankItem->bank;
        abort_if(!$course->questionBanks()->where('question_banks.id', $bank->id)->exists(), 404);
        $questionBankItem->delete();
        return redirect()->route('courses.question-bank.index', $course)
            ->with('success', 'Question removed from bank.');
    }
}
