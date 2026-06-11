<?php

namespace App\Exports;

use App\Models\Announcement;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnnouncementsExport
{
    public function download(?int $courseId = null): StreamedResponse
    {
        $query = Announcement::with(['author', 'course']);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $announcements = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="announcements-export-' . now()->format('Y-m-d-His') . '.csv"',
        ];

        $callback = function () use ($announcements) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Title',
                'Content',
                'Priority',
                'Course',
                'Author',
                'Created At',
            ]);

            foreach ($announcements as $a) {
                fputcsv($handle, [
                    $a->title,
                    $a->content,
                    $a->priority,
                    $a->course?->title ?? 'Global',
                    $a->author?->name ?? '',
                    $a->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
