<?php

namespace App\Http\Controllers\Admin;

use App\Imports\QuestionBanksImport;
use App\Imports\QuizQuestionsImport;
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

    public function importQuestions(Request $request, QuestionBank $questionBank)
    {
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
}
