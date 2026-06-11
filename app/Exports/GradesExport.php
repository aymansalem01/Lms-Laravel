<?php

namespace App\Exports;

use App\Models\Grade;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GradesExport
{
    public function download(?int $courseId = null): StreamedResponse
    {
        $query = Grade::with([
            'submission.student',
            'submission.assignment.course',
        ]);

        if ($courseId) {
            $query->whereHas('submission.assignment', fn($q) => $q->where('course_id', $courseId));
        }

        $grades = $query->orderBy('created_at')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="grades-export-' . now()->format('Y-m-d-His') . '.csv"',
        ];

        $callback = function () use ($grades) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Student Name',
                'Student Email',
                'Course',
                'Assignment',
                'Assignment Max Score',
                'Score',
                'Feedback',
                'Status',
                'Submitted At',
                'Graded At',
            ]);

            foreach ($grades as $grade) {
                $sub = $grade->submission;
                $assignment = $sub?->assignment;
                fputcsv($handle, [
                    $sub?->student?->name ?? '',
                    $sub?->student?->email ?? '',
                    $assignment?->course?->title ?? '',
                    $assignment?->title ?? '',
                    $assignment?->max_score ?? '',
                    $grade->score,
                    $grade->feedback ?? '',
                    $sub?->status ?? '',
                    $sub?->submitted_at ? Carbon::parse($sub->submitted_at)->format('Y-m-d H:i:s') : '',
                    $grade->graded_at ? Carbon::parse($grade->graded_at)->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function preview(int $limit = 5): Collection
    {
        return Grade::with([
            'submission.student',
            'submission.assignment.course',
        ])->latest('graded_at')->limit($limit)->get()->map(fn($g) => [
            'student_name' => $g->submission?->student?->name ?? '',
            'student_email' => $g->submission?->student?->email ?? '',
            'course' => $g->submission?->assignment?->course?->title ?? '',
            'assignment' => $g->submission?->assignment?->title ?? '',
            'score' => $g->score,
            'feedback' => $g->feedback ?? '',
        ]);
    }
}
