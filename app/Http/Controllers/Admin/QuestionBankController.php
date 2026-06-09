<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionBank;
use App\Models\QuestionBankItem;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $query = QuestionBank::with(['user', 'courses', 'items'])->latest();

        if ($request->filled('course_id')) {
            $query->whereHas('courses', fn($q) => $q->where('courses.id', $request->course_id));
        }

        $banks = $query->paginate(20);
        $courses = Course::orderBy('title')->get();

        return view('admin.question-bank.index', compact('banks', 'courses'));
    }

    public function create()
    {
        $courses = Course::orderBy('title')->get();
        return view('admin.question-bank.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                     => 'required|string|max:255',
            'course_ids'               => 'required_without:is_visible_to_all|array',
            'course_ids.*'             => 'exists:courses,id',
            'is_visible_to_all'        => 'nullable|boolean',
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
            'is_visible_to_all' => $request->boolean('is_visible_to_all'),
        ]);

        if (!$bank->is_visible_to_all && !empty($data['course_ids'])) {
            $bank->courses()->attach($data['course_ids']);
        }

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
        return redirect()->route('admin.question-bank.index')
            ->with('success', "Bank \"{$bank->name}\" created with $count questions.");
    }

    public function show(QuestionBank $questionBank)
    {
        $questionBank->load(['user', 'courses', 'items.user']);
        return view('admin.question-bank.show', compact('questionBank'));
    }

    public function edit(QuestionBank $questionBank)
    {
        $questionBank->load('items');
        $courses = Course::orderBy('title')->get();
        return view('admin.question-bank.edit', compact('questionBank', 'courses'));
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        $data = $request->validate([
            'name'                     => 'required|string|max:255',
            'course_ids'               => 'nullable|array',
            'course_ids.*'             => 'exists:courses,id',
            'is_visible_to_all'        => 'nullable|boolean',
        ]);

        $questionBank->update([
            'name'              => $data['name'],
            'is_visible_to_all' => $request->boolean('is_visible_to_all'),
        ]);

        if ($questionBank->is_visible_to_all) {
            $questionBank->courses()->detach();
        } else {
            $questionBank->courses()->sync($data['course_ids'] ?? []);
        }

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Bank updated.');
    }

    public function destroy(QuestionBank $questionBank)
    {
        $questionBank->delete();
        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Bank deleted.');
    }
}
