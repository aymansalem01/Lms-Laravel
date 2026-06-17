<?php

namespace App\Http\Controllers;

use App\Mail\QuizGradeReleased;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuestionBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        $quizzes = $course->quizzes()->withCount('questions')->get();
        return view('quizzes.index', compact('course', 'quizzes'));
    }

    public function show(Course $course, Quiz $quiz)
    {
        $user = auth()->user();

        if ($user->isStudent()) {
            $attempts = $quiz->attempts()->where('student_id', $user->id)->get();
            $canAttempt = is_null($quiz->max_attempts) || $attempts->count() < $quiz->max_attempts;
            return view('quizzes.show', compact('course', 'quiz', 'attempts', 'canAttempt'));
        }

        $quiz->load('questions');
        $attempts = $quiz->attempts()->with('student')->get();
        return view('quizzes.show', compact('course', 'quiz', 'attempts'));
    }

    public function take(Course $course, Quiz $quiz)
    {
        $user = auth()->user();

        $existingAttempts = $quiz->attempts()->where('student_id', $user->id)->count();
        abort_if(!is_null($quiz->max_attempts) && $existingAttempts >= $quiz->max_attempts, 403, 'Max attempts reached.');

        $quiz->load('questions');

        if ($quiz->questions->isEmpty()) {
            return redirect()->route('courses.quizzes.show', [$course, $quiz])
                ->with('error', 'This quiz has no questions yet.');
        }

        return view('quizzes.take', compact('course', 'quiz'));
    }

    public function create(Course $course)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $modules = $course->modules ?? collect();
        return view('quizzes.create', compact('course', 'modules'));
    }

    public function store(Request $request, Course $course)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'max_attempts'      => 'nullable|integer|min:1',
            'time_limit'        => 'nullable|integer|min:0',
            'is_published'      => 'nullable|boolean',
            'module_id'         => 'nullable|exists:modules,id',
            'bank_pulls'        => 'nullable|array',
            'bank_pulls.*'      => 'nullable|integer|min:0',
            'show_results'      => 'nullable|boolean',
        ]);

        $data['show_results'] = $request->boolean('show_results');
        $data['module_id'] = $request->filled('module_id') ? $data['module_id'] : null;
        $quiz = $course->quizzes()->create($data);

        if ($request->has('questions')) {
            foreach ($request->input('questions') as $order => $q) {
                $quiz->questions()->create([
                    'type'           => $q['type'],
                    'question'       => $q['question'],
                    'options'        => $q['options'] ?? null,
                    'correct_answer' => $q['correct_answer'] ?? null,
                    'points'         => $q['points'] ?? 1,
                    'order_index'    => $order,
                ]);
            }
        }

        $importedBankIds = collect();
        if ($request->has('bank_item_ids')) {
            $bankIds = $course->questionBanks()->pluck('question_banks.id');
            $globalBankIds = QuestionBank::where('is_visible_to_all', true)->pluck('id');
            $allowedBankIds = $bankIds->merge($globalBankIds)->unique();
            $items = \App\Models\QuestionBankItem::whereIn('id', $request->input('bank_item_ids'))
                ->whereIn('question_bank_id', $allowedBankIds)
                ->get();
            $order = $quiz->questions()->count();
            foreach ($items as $item) {
                $quiz->questions()->create([
                    'bank_item_id'   => $item->id,
                    'type'           => $item->type,
                    'question'       => $item->question,
                    'options'        => $item->options,
                    'correct_answer' => $item->correct_answer,
                    'points'         => $item->points,
                    'order_index'    => $order++,
                ]);
                $importedBankIds->push($item->id);
            }
        }

        if ($request->has('bank_pulls')) {
            $order = $quiz->questions()->count();
            foreach ($request->input('bank_pulls') as $bankId => $count) {
                $count = (int) $count;
                if ($count <= 0) continue;
                $randomItems = \App\Models\QuestionBankItem::where('question_bank_id', $bankId)
                    ->whereNotIn('id', $importedBankIds)
                    ->inRandomOrder()
                    ->take($count)
                    ->get();
                foreach ($randomItems as $item) {
                    $quiz->questions()->create([
                        'bank_item_id'   => $item->id,
                        'type'           => $item->type,
                        'question'       => $item->question,
                        'options'        => $item->options,
                        'correct_answer' => $item->correct_answer,
                        'points'         => $item->points,
                        'order_index'    => $order++,
                    ]);
                }
            }
        }

        $course->students()->each(function ($student) use ($course, $quiz) {
            $student->notifications()->create([
                'type'    => 'quiz',
                'title'   => 'New Quiz: ' . $quiz->title,
                'message' => 'A new quiz "' . $quiz->title . '" has been created in ' . $course->title,
                'link'    => route('courses.quizzes.show', [$course, $quiz]),
            ]);
        });

        return redirect()->route('courses.quizzes.index', $course)
            ->with('success', 'Quiz created successfully.');
    }

    public function edit(Course $course, Quiz $quiz)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $quiz->load('questions');
        $modules = $course->modules ?? collect();
        return view('quizzes.edit', compact('course', 'quiz', 'modules'));
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'max_attempts'      => 'nullable|integer|min:1',
            'time_limit'        => 'nullable|integer|min:0',
            'is_published'      => 'nullable|boolean',
            'module_id'         => 'nullable|exists:modules,id',
            'bank_pulls'        => 'nullable|array',
            'bank_pulls.*'      => 'nullable|integer|min:0',
            'show_results'      => 'nullable|boolean',
        ]);

        $data['show_results'] = $request->boolean('show_results');
        $data['module_id'] = $request->filled('module_id') ? $data['module_id'] : null;
        $quiz->update($data);

        $quiz->questions()->delete();

        if ($request->has('questions')) {
            foreach ($request->input('questions') as $order => $q) {
                $quiz->questions()->create([
                    'type'           => $q['type'],
                    'question'       => $q['question'],
                    'options'        => $q['options'] ?? null,
                    'correct_answer' => $q['correct_answer'] ?? null,
                    'points'         => $q['points'] ?? 1,
                    'order_index'    => $order,
                ]);
            }
        }

        $importedBankIds = collect();
        if ($request->has('bank_item_ids')) {
            $bankIds = $course->questionBanks()->pluck('question_banks.id');
            $globalBankIds = QuestionBank::where('is_visible_to_all', true)->pluck('id');
            $allowedBankIds = $bankIds->merge($globalBankIds)->unique();
            $items = \App\Models\QuestionBankItem::whereIn('id', $request->input('bank_item_ids'))
                ->whereIn('question_bank_id', $allowedBankIds)
                ->get();
            $order = $quiz->questions()->count();
            foreach ($items as $item) {
                $quiz->questions()->create([
                    'bank_item_id'   => $item->id,
                    'type'           => $item->type,
                    'question'       => $item->question,
                    'options'        => $item->options,
                    'correct_answer' => $item->correct_answer,
                    'points'         => $item->points,
                    'order_index'    => $order++,
                ]);
                $importedBankIds->push($item->id);
            }
        }

        if ($request->has('bank_pulls')) {
            $order = $quiz->questions()->count();
            foreach ($request->input('bank_pulls') as $bankId => $count) {
                $count = (int) $count;
                if ($count <= 0) continue;
                $randomItems = \App\Models\QuestionBankItem::where('question_bank_id', $bankId)
                    ->whereNotIn('id', $importedBankIds)
                    ->inRandomOrder()
                    ->take($count)
                    ->get();
                foreach ($randomItems as $item) {
                    $quiz->questions()->create([
                        'bank_item_id'   => $item->id,
                        'type'           => $item->type,
                        'question'       => $item->question,
                        'options'        => $item->options,
                        'correct_answer' => $item->correct_answer,
                        'points'         => $item->points,
                        'order_index'    => $order++,
                    ]);
                }
            }
        }

        return redirect()->route('courses.quizzes.index', $course)
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $quiz->delete();

        return redirect()->route('courses.quizzes.index', $course)
            ->with('success', 'Quiz deleted successfully.');
    }

    public function attempt(Request $request, Course $course, Quiz $quiz)
    {
        $user = auth()->user();

        $existingAttempts = $quiz->attempts()->where('student_id', $user->id)->count();
        abort_if(!is_null($quiz->max_attempts) && $existingAttempts >= $quiz->max_attempts, 403, 'Max attempts reached.');

        $questions = $quiz->randomize_questions ? $quiz->questions->shuffle() : $quiz->questions;
        $answers = $request->input('answers', []);
        $totalScore = 0;
        $maxScore = $questions->sum('points');

        foreach ($questions as $qIndex => $question) {
            $userAnswer = $answers[$qIndex] ?? null;

            if (in_array($question->type, ['multiple_choice', 'true_false'])) {
                $isCorrect = strtolower((string) $userAnswer) === strtolower((string) $question->correct_answer);
                if ($isCorrect) {
                    $totalScore += $question->points;
                }
            }
        }

        $attempt = $quiz->attempts()->create([
            'student_id'   => $user->id,
            'answers'      => $answers,
            'score'        => $totalScore,
            'max_score'    => $maxScore,
            'submitted_at' => now(),
            'is_draft'     => false,
        ]);

        if ($quiz->show_results) {
            return redirect()->route('courses.quizzes.results', [$course, $quiz, $attempt])
                ->with('success', 'Quiz submitted successfully.');
        }

        return redirect()->route('courses.quizzes.show', [$course, $quiz])
            ->with('success', 'Quiz submitted successfully. Your instructor will review your results.');
    }

    public function results(Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        $user = auth()->user();
        abort_if($attempt->student_id !== $user->id && $user->isStudent(), 403);

        if (!$quiz->show_results && $user->isStudent()) {
            return redirect()->route('courses.quizzes.show', [$course, $quiz])
                ->with('info', 'Your results are not yet available.');
        }

        $attempt->load('quiz.questions');
        return view('quizzes.results', compact('course', 'quiz', 'attempt'));
    }

    public function review(Course $course, Quiz $quiz)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $quiz->loadCount('questions');

        $students = $course->students()
            ->with(['quizAttempts' => function ($q) use ($quiz) {
                $q->where('quiz_id', $quiz->id);
            }])
            ->get();

        $totalSubmissions = $students->filter(fn($s) => $s->quizAttempts->isNotEmpty())->count();
        $gradedCount = $students->filter(fn($s) => $s->quizAttempts->first()?->released_at)->count();

        return view('quizzes.review', compact('course', 'quiz', 'students', 'totalSubmissions', 'gradedCount'));
    }

    public function releaseGrade(Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $attempt->update(['released_at' => now()]);

        $attempt->load('student', 'quiz');
        Mail::to($attempt->student->email)->send(new QuizGradeReleased(
            studentName: $attempt->student->name,
            courseTitle: $course->title,
            quizTitle: $quiz->title,
            score: $attempt->score,
            maxScore: $attempt->max_score,
            courseId: $course->id,
        ));
        $attempt->student->notifications()->create([
            'type'    => 'quiz_grade',
            'title'   => 'Quiz Grade Released: ' . $quiz->title,
            'message' => 'Your grade for quiz "' . $quiz->title . '" in ' . $course->title . ' has been released. Score: ' . $attempt->score . '/' . $attempt->max_score,
            'link'    => route('courses.quizzes.show', [$course, $quiz]),
        ]);

        return redirect()->route('quizzes.show', [$course, $quiz])
            ->with('success', 'Quiz grade released successfully.');
    }
}
