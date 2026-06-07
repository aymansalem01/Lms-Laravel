<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionBankItem;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $query = QuestionBankItem::with(['course', 'user'])->latest();

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $items = $query->paginate(20);
        $courses = Course::orderBy('title')->get();

        return view('admin.question-bank.index', compact('items', 'courses'));
    }

    public function create()
    {
        $courses = Course::orderBy('title')->get();
        return view('admin.question-bank.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'      => 'required|exists:courses,id',
            'type'           => 'required|in:multiple_choice,true_false,short_answer,long_answer',
            'question'       => 'required|string',
            'options'        => 'nullable|array',
            'options.*'      => 'nullable|string',
            'correct_answer' => 'nullable|string',
            'points'         => 'required|integer|min:1',
        ]);

        $course = Course::findOrFail($data['course_id']);

        $course->questionBankItems()->create([
            'user_id'        => auth()->id(),
            'type'           => $data['type'],
            'question'       => $data['question'],
            'options'        => $data['type'] === 'multiple_choice' ? array_values(array_filter($data['options'] ?? [])) : null,
            'correct_answer' => $data['correct_answer'],
            'points'         => $data['points'],
        ]);

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Question added to bank.');
    }

    public function edit(QuestionBankItem $questionBankItem)
    {
        $courses = Course::orderBy('title')->get();
        return view('admin.question-bank.edit', compact('questionBankItem', 'courses'));
    }

    public function update(Request $request, QuestionBankItem $questionBankItem)
    {
        $data = $request->validate([
            'course_id'      => 'required|exists:courses,id',
            'type'           => 'required|in:multiple_choice,true_false,short_answer,long_answer',
            'question'       => 'required|string',
            'options'        => 'nullable|array',
            'options.*'      => 'nullable|string',
            'correct_answer' => 'nullable|string',
            'points'         => 'required|integer|min:1',
        ]);

        $questionBankItem->update([
            'course_id'      => $data['course_id'],
            'type'           => $data['type'],
            'question'       => $data['question'],
            'options'        => $data['type'] === 'multiple_choice' ? array_values(array_filter($data['options'] ?? [])) : null,
            'correct_answer' => $data['correct_answer'],
            'points'         => $data['points'],
        ]);

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Question updated.');
    }

    public function destroy(QuestionBankItem $questionBankItem)
    {
        $questionBankItem->delete();
        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Question removed from bank.');
    }
}
