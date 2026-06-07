<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\QuestionBankItem;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    public function index(Course $course)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $items = $course->questionBankItems()->with('user')->latest()->paginate(20);
        return view('question-bank.index', compact('course', 'items'));
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
            'type'           => 'required|in:multiple_choice,true_false,short_answer,long_answer',
            'question'       => 'required|string',
            'options'        => 'nullable|array',
            'options.*'      => 'nullable|string',
            'correct_answer' => 'nullable|string',
            'points'         => 'required|integer|min:1',
        ]);

        $course->questionBankItems()->create([
            'user_id'        => auth()->id(),
            'type'           => $data['type'],
            'question'       => $data['question'],
            'options'        => $data['type'] === 'multiple_choice' ? array_values(array_filter($data['options'] ?? [])) : null,
            'correct_answer' => $data['correct_answer'],
            'points'         => $data['points'],
        ]);

        return redirect()->route('courses.question-bank.index', $course)
            ->with('success', 'Question added to bank.');
    }

    public function edit(Course $course, QuestionBankItem $questionBankItem)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        abort_if($questionBankItem->course_id !== $course->id, 404);
        return view('question-bank.edit', compact('course', 'questionBankItem'));
    }

    public function update(Request $request, Course $course, QuestionBankItem $questionBankItem)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        abort_if($questionBankItem->course_id !== $course->id, 404);

        $data = $request->validate([
            'type'           => 'required|in:multiple_choice,true_false,short_answer,long_answer',
            'question'       => 'required|string',
            'options'        => 'nullable|array',
            'options.*'      => 'nullable|string',
            'correct_answer' => 'nullable|string',
            'points'         => 'required|integer|min:1',
        ]);

        $questionBankItem->update([
            'type'           => $data['type'],
            'question'       => $data['question'],
            'options'        => $data['type'] === 'multiple_choice' ? array_values(array_filter($data['options'] ?? [])) : null,
            'correct_answer' => $data['correct_answer'],
            'points'         => $data['points'],
        ]);

        return redirect()->route('courses.question-bank.index', $course)
            ->with('success', 'Question updated.');
    }

    public function destroy(Course $course, QuestionBankItem $questionBankItem)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        abort_if($questionBankItem->course_id !== $course->id, 404);
        $questionBankItem->delete();
        return redirect()->route('courses.question-bank.index', $course)
            ->with('success', 'Question removed from bank.');
    }
}
