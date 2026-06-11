<?php

namespace App\Exports;

use App\Models\Submission;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionsExport
{
    public function download(?int $courseId = null, ?string $status = null): StreamedResponse
    {
        $query = Submission::with(['student', 'assignment.course', 'grade']);

        if ($courseId) {
            $query->whereHas('assignment', fn($q) => $q->where('course_id', $courseId));
        }

        if ($status) {
            $query->where('status', $status);
        }

        $submissions = $query->latest('submitted_at')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="submissions-export-' . now()->format('Y-m-d-His') . '.csv"',
        ];

        $callback = function () use ($submissions) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Student Name',
                'Student Email',
                'Course',
                'Assignment',
                'Status',
                'Score',
                'Feedback',
                'Submitted At',
            ]);

            foreach ($submissions as $s) {
                fputcsv($handle, [
                    $s->student?->name ?? '',
                    $s->student?->email ?? '',
                    $s->assignment?->course?->title ?? '',
                    $s->assignment?->title ?? '',
                    $s->status,
                    $s->grade?->score ?? '',
                    $s->grade?->feedback ?? '',
                    $s->submitted_at ? Carbon::parse($s->submitted_at)->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
