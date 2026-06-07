<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.grades.index', compact('grades', 'stats'));
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
}
