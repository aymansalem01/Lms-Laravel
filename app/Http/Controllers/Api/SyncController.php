<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseAttendance;
use App\Models\Grade;
use App\Services\UserSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function __construct(
        private UserSyncService $sync,
    ) {}

    /**
     * POST /api/v1/sync/user
     * Receives a user sync request from SIS_CRM.
     * Creates or updates the local user record.
     */
    public function upsertUser(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:255'],
            'password_hash' => ['required', 'string'],
            'role' => ['required', 'string', 'in:student,instructor,admin'],
        ]);

        $user = $this->sync->upsertUser($request->only([
            'email', 'name', 'password_hash', 'role',
            'sis_crm_user_id', 'sis_student_id', 'sso_provider',
        ]));

        if (! $user) {
            return response()->json(['message' => 'Failed to sync user.'], 500);
        }

        return response()->json([
            'status' => 'ok',
            'user_id' => $user->id,
        ]);
    }

    /**
     * DELETE /api/v1/sync/user/{id}
     * Deactivates a user on the LMS (called by SIS_CRM).
     */
    public function deactivateUser(int $id): JsonResponse
    {
        $deleted = $this->sync->deactivateUser($id);

        if (! $deleted) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * GET /api/v1/courses/{id}/grades
     * Returns grades for a course (consumed by SIS_CRM).
     */
    public function courseGrades(int $id): JsonResponse
    {
        $grades = Grade::whereHas('submission', fn ($q) => $q->where('assignment_id', $id))
            ->with('submission.assignment', 'student')
            ->get()
            ->map(fn ($g) => [
                'student_email' => $g->student->email ?? null,
                'score' => $g->score,
                'feedback' => $g->feedback,
                'graded_at' => $g->graded_at,
            ]);

        return response()->json(['data' => $grades]);
    }

    /**
     * GET /api/v1/courses/{id}/attendance
     * Returns attendance for a course (consumed by SIS_CRM).
     */
    public function courseAttendance(int $id): JsonResponse
    {
        $attendance = CourseAttendance::where('course_id', $id)
            ->with('student')
            ->get()
            ->map(fn ($a) => [
                'student_email' => $a->student->email ?? null,
                'status' => $a->status,
                'date' => $a->created_at,
            ]);

        return response()->json(['data' => $attendance]);
    }
}
