<?php

namespace App\Exports;

use App\Models\Course;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GradeRulesExport
{
    public function download(Course $course): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="grade-rules-' . str_replace(' ', '-', $course->title) . '-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($course) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Course', 'Category', 'Weight (%)']);
            $course->load('gradeRules');
            $categories = ['quiz', 'assignment', 'attendance'];

            foreach ($categories as $cat) {
                $rule = $course->gradeRules->firstWhere('category', $cat);
                fputcsv($handle, [
                    $course->title,
                    ucfirst($cat),
                    $rule ? $rule->weight : 0,
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
