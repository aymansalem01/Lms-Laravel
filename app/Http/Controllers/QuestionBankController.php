<?php

namespace App\Http\Controllers;

use App\Imports\QuestionBanksImport;
use App\Imports\QuizQuestionsImport;
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
        $courses = auth()->user()->isAdmin()
            ? Course::orderBy('title')->get(['id', 'title'])
            : auth()->user()->taughtCourses()->orderBy('title')->get(['id', 'title']);
        return view('question-bank.index', compact('course', 'banks', 'courses'));
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

    public function importQuestions(Request $request, QuestionBank $questionBank)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle);

        if (!$header || count(array_intersect(array_map('trim', $header), ['type', 'question'])) < 2) {
            fclose($handle);
            return back()->withErrors(['csv_file' => "CSV must have 'type', 'question', and 'points' columns. Found: " . implode(', ', $header ?? [])]);
        }

        $header = array_map('trim', $header);
        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            $row = [];
            foreach ($header as $i => $col) {
                $row[$col] = $line[$i] ?? '';
            }
            $rows[] = $row;
        }

        fclose($handle);

        if (empty($rows)) {
            return back()->withErrors(['csv_file' => 'CSV file is empty.']);
        }

        $results = app(QuizQuestionsImport::class)->import($questionBank, $rows);

        if ($results['failed'] > 0) {
            $message = "Imported {$results['succeeded']} questions. {$results['failed']} rows failed.";
            return back()->with('warning', $message)->with('import_errors', $results['errors']);
        }

        return back()->with('success', "All {$results['succeeded']} questions imported successfully.");
    }

    public function downloadImportExample()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="quiz-questions-import-example.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['type', 'question', 'options', 'correct_answer', 'points']);
            fputcsv($handle, ['multiple_choice', 'What is 2+2?', '1|2|3|4', '4', '1']);
            fputcsv($handle, ['multiple_choice', 'Which planet is known as the Red Planet?', 'Venus|Mars|Jupiter|Saturn', 'Mars', '2']);
            fputcsv($handle, ['true_false', 'The Earth is flat.', '', 'false', '1']);
            fputcsv($handle, ['true_false', 'Water freezes at 0 degrees Celsius.', '', 'true', '1']);
            fputcsv($handle, ['short_answer', 'What is the chemical symbol for water?', '', 'H2O', '2']);
            fputcsv($handle, ['long_answer', 'Explain the process of photosynthesis.', '', '', '5']);
            fclose($handle);
        };

        return new \Symfony\Component\HttpFoundation\StreamedResponse($callback, 200, $headers);
    }

    public function bulkImportBanks(Request $request)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle);

        if (!$header || count(array_intersect(array_map('trim', $header), ['bank_name', 'type', 'question'])) < 3) {
            fclose($handle);
            return back()->withErrors(['csv_file' => "CSV must have 'bank_name', 'type', 'question', and 'points' columns."]);
        }

        $header = array_map('trim', $header);
        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            $row = [];
            foreach ($header as $i => $col) {
                $row[$col] = $line[$i] ?? '';
            }
            $rows[] = $row;
        }

        fclose($handle);

        if (empty($rows)) {
            return back()->withErrors(['csv_file' => 'CSV file is empty.']);
        }

        if ($request->filled('course_ids')) {
            $selected = implode('|', $request->course_ids);
            foreach ($rows as &$row) {
                $row['course_ids'] = $selected;
            }
        }

        $results = app(QuestionBanksImport::class)->import($rows);

        $msg = "Imported {$results['succeeded']} questions across {$results['banks_created']} bank(s).";

        if ($results['failed'] > 0) {
            return back()->with('warning', $msg . " {$results['failed']} rows failed.")->with('import_errors', $results['errors']);
        }

        return back()->with('success', $msg);
    }

    public function downloadBulkImportExample()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="question-banks-bulk-import-example.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['bank_name', 'type', 'question', 'options', 'correct_answer', 'points']);
            fputcsv($handle, ['Math Quiz', 'multiple_choice', 'What is 2+2?', '1|2|3|4', '4', '1']);
            fputcsv($handle, ['Math Quiz', 'true_false', 'Is 10 greater than 5?', '', 'true', '1']);
            fputcsv($handle, ['Math Quiz', 'short_answer', 'What is 5+3?', '', '8', '2']);
            fputcsv($handle, ['Science Quiz', 'multiple_choice', 'Which planet is closest to the Sun?', 'Venus|Mercury|Earth|Mars', 'Mercury', '2']);
            fputcsv($handle, ['Science Quiz', 'true_false', 'Water boils at 100C.', '', 'true', '1']);
            fclose($handle);
        };

        return new \Symfony\Component\HttpFoundation\StreamedResponse($callback, 200, $headers);
    }

    public function editItem(QuestionBankItem $questionBankItem)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $questionBankItem->load('bank');
        return view('question-bank.edit', ['questionBankItem' => $questionBankItem, 'course' => $questionBankItem->bank->courses->first()]);
    }

    public function updateItem(Request $request, QuestionBankItem $questionBankItem)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

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

        return redirect()->route('question-bank.index')
            ->with('success', 'Question updated.');
    }

    public function destroyItem(QuestionBankItem $questionBankItem)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }
        $questionBankItem->delete();
        return redirect()->route('question-bank.index')
            ->with('success', 'Question removed from bank.');
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
