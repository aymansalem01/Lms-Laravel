<?php

namespace App\Exports;

use App\Models\Enrollment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EnrollmentsExport
{
    public function download(?int $courseId = null, ?string $program = null, ?string $search = null): StreamedResponse
    {
        $query = Enrollment::with(['student', 'course.instructor'])
            ->latest('enrolled_at');

        if ($courseId) $query->where('course_id', $courseId);
        if ($program) $query->whereHas('student', fn($q) => $q->where('program', $program));
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('course', function ($cq) use ($search) {
                    $cq->where('title', 'like', "%{$search}%");
                });
            });
        }

        $enrollments = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="enrollments-export-' . now()->format('Y-m-d-His') . '.csv"',
        ];

        $callback = function () use ($enrollments) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Student Name',
                'Student Email',
                'Student Program',
                'Course',
                'Instructor',
                'Enrolled At',
            ]);

            foreach ($enrollments as $enrollment) {
                fputcsv($handle, [
                    $enrollment->student?->name ?? '',
                    $enrollment->student?->email ?? '',
                    $enrollment->student?->program ?? '',
                    $enrollment->course?->title ?? '',
                    $enrollment->course?->instructor?->name ?? '',
                    $enrollment->enrolled_at ? \Illuminate\Support\Carbon::parse($enrollment->enrolled_at)->format('Y-m-d H:i:s') : $enrollment->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
