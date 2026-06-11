<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GradesExport;
use App\Imports\GradesImport;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::with([
            'submission.student',
            'submission.assignment.course',
            'instructor',
        ])->latest('graded_at');

        if ($search = $request->input('search')) {
            $query->whereHas('submission.student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($courseId = $request->input('course_id')) {
            $query->whereHas('submission.assignment', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        if ($minScore = $request->input('min_score')) {
            $query->where('score', '>=', $minScore);
        }

        if ($maxScore = $request->input('max_score')) {
            $query->where('score', '<=', $maxScore);
        }

        $grades = $query->paginate(30)->withQueryString();

        $stats = [
            'total'   => Grade::count(),
            'avg'     => round(Grade::avg('score') ?? 0, 1),
            'highest' => Grade::max('score') ?? 0,
            'lowest'  => Grade::min('score') ?? 0,
        ];

        $courses = Course::orderBy('title')->pluck('title', 'id');

        return view('admin.grades.index', compact('grades', 'stats', 'courses'));
    }

    public function show(Grade $grade)
    {
        $grade->load([
            'submission.student',
            'submission.assignment.course',
            'submission.submissionFingerprint',
            'instructor',
        ]);

        return view('admin.grades.show', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $data = $request->validate([
            'score'    => ['required', 'numeric', 'min:0'],
            'feedback' => ['nullable', 'string'],
        ]);

        $grade->update([
            'score'       => $data['score'],
            'feedback'    => $data['feedback'],
            'graded_at'   => now(),
            'instructor_id' => Auth::id(),
        ]);

        $grade->submission->update(['status' => 'graded']);

        return back()->with('success', 'Grade updated.');
    }

    public function destroy(Grade $grade)
    {
        $grade->submission->update(['status' => 'submitted']);
        $grade->delete();

        return back()->with('success', 'Grade removed. Submission reverted to ungraded.');
    }

    public function export(Request $request)
    {
        return app(GradesExport::class)->download($request->input('course_id'));
    }

    public function import(Request $request)
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

        if (!$header || count(array_intersect(array_map('trim', $header), ['student_email', 'score'])) < 2) {
            fclose($handle);
            return back()->withErrors(['csv_file' => "CSV must have 'student_email', 'assignment_title', and 'score' columns. Found: " . implode(', ', $header ?? [])]);
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

        $results = app(GradesImport::class)->import($rows);

        if ($results['failed'] > 0) {
            $message = "Imported {$results['succeeded']} grades. {$results['failed']} rows failed.";
            return back()->with('warning', $message)->with('import_errors', $results['errors']);
        }

        return back()->with('success', "All {$results['succeeded']} grades imported successfully.");
    }

    public function downloadExample()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="grades-import-example.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['student_email', 'assignment_title', 'score', 'feedback']);
            fputcsv($handle, ['student@example.com', 'Week 1 Essay', '85', 'Good work, but needs more detail in the conclusion.']);
            fputcsv($handle, ['another.student@example.com', 'Final Project', '92', 'Excellent project!']);
            fputcsv($handle, ['third@example.com', 'Week 1 Essay', '67', 'See me after class.']);
            fclose($handle);
        };

        return new \Symfony\Component\HttpFoundation\StreamedResponse($callback, 200, $headers);
    }

}
