<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SubmissionsExport;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::with([
            'student',
            'assignment.course.instructor',
            'grade',
        ])->latest('submitted_at');

        if ($search = $request->input('search')) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($assignmentId = $request->input('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        if ($courseId = $request->input('course_id')) {
            $query->whereHas('assignment', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $submissions = $query->paginate(30)->withQueryString();

        $stats = [
            'total'  => Submission::count(),
            'pending' => Submission::where('status', 'submitted')->count(),
            'graded'  => Submission::where('status', 'graded')->count(),
            'late'    => Submission::where('status', 'late')->count(),
        ];

        $assignments = Assignment::with('course')->orderBy('title')->get(['id', 'title', 'course_id']);

        return view('admin.submissions.index', compact('submissions', 'stats', 'assignments'));
    }

    public function show(Submission $submission)
    {
        $submission->load([
            'student',
            'assignment.course.instructor',
            'grade.instructor',
            'plagiarismReport',
        ]);

        return view('admin.submissions.show', compact('submission'));
    }

    public function destroy(Submission $submission)
    {
        $submission->delete();

        return back()->with('success', 'Submission deleted.');
    }

    public function export(Request $request)
    {
        return app(SubmissionsExport::class)->download(
            $request->input('course_id'),
            $request->input('status'),
        );
    }

    public function downloadExample()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="submissions-import-example.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Student Name', 'Student Email', 'Course', 'Assignment', 'Status', 'Score', 'Feedback', 'Submitted At']);
            fputcsv($handle, ['John Doe', 'john@example.com', 'Film 101', 'Week 1 Essay', 'graded', '85', 'Good work!', '2026-06-01 10:00:00']);
            fputcsv($handle, ['Jane Smith', 'jane@example.com', 'Film 101', 'Week 1 Essay', 'submitted', '', '', '2026-06-02 14:30:00']);
            fputcsv($handle, ['Bob Wilson', 'bob@example.com', 'Web Design', 'Final Project', 'graded', '92', 'Excellent!', '2026-06-03 09:15:00']);
            fclose($handle);
        };

        return new \Symfony\Component\HttpFoundation\StreamedResponse($callback, 200, $headers);
    }
}
