<?php

namespace App\Exports;

use App\Models\Course;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceExport
{
    public function download(Course $course, ?string $date = null): StreamedResponse
    {
        $query = $course->attendance()->with('student');

        if ($date) {
            $query->where('date', $date);
        }

        $records = $query->orderBy('date', 'desc')->orderBy('student_id')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="attendance-' . str_replace(' ', '-', $course->title) . '-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Student Name',
                'Student Email',
                'Course',
                'Date',
                'Status',
            ]);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->student?->name ?? '',
                    $record->student?->email ?? '',
                    $record->course?->title ?? '',
                    $record->date instanceof Carbon ? $record->date->format('Y-m-d') : $record->date,
                    $record->status,
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
