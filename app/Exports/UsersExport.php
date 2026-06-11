<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UsersExport
{
    public function download(?string $role = null, ?string $program = null, ?string $search = null): StreamedResponse
    {
        $query = User::query()
            ->withCount(['enrollments as enrolled_courses_count'])
            ->latest();

        if ($role) $query->where('role', $role);
        if ($program) $query->where('program', $program);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="users-export-' . now()->format('Y-m-d-His') . '.csv"',
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Name',
                'Email',
                'Role',
                'Program',
                'Verified',
                'Enrolled Courses',
                'Created At',
            ]);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->program ?? '',
                    $user->is_verified ? 'Yes' : 'No',
                    $user->enrolled_courses_count ?? 0,
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
