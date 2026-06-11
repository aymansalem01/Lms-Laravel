<?php

namespace App\Exports;

use App\Models\Quiz;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuizAttemptsExport
{
    public function download(Quiz $quiz): StreamedResponse
    {
        $quiz->load(['attempts.student', 'course']);
        $attempts = $quiz->attempts;

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="quiz-attempts-' . str_replace(' ', '-', $quiz->title) . '-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($quiz, $attempts) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Quiz',
                'Course',
                'Student Name',
                'Student Email',
                'Score',
                'Max Score',
                'Percentage',
                'Is Draft',
                'Submitted At',
                'Released At',
            ]);

            foreach ($attempts as $a) {
                $pct = $a->max_score > 0 ? round(($a->score / $a->max_score) * 100, 1) : 0;
                fputcsv($handle, [
                    $quiz->title,
                    $quiz->course?->title ?? '',
                    $a->student?->name ?? '',
                    $a->student?->email ?? '',
                    $a->score ?? '',
                    $a->max_score ?? '',
                    $pct,
                    $a->is_draft ? 'Yes' : 'No',
                    $a->submitted_at ? Carbon::parse($a->submitted_at)->format('Y-m-d H:i:s') : '',
                    $a->released_at ? Carbon::parse($a->released_at)->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
