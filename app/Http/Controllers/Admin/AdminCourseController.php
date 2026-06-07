<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin CourseController
 *
 * Mirrors Next.js: app/(dashboard)/admin/courses/page.tsx
 *                  app/(dashboard)/courses/[courseId]/admin/page.tsx
 *
 * Responsibilities:
 *  - List ALL courses across all instructors with enrollment counts
 *  - Filter/search courses
 *  - Toggle publish/unpublish any course
 *  - Reassign course instructor
 *  - Force-delete any course
 *  - Course-level admin panel (per-course)
 */
class AdminCourseController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    /**
     * GET /admin/courses
     *
     * Lists every course on the platform with instructor info,
     * enrollment count, and publish status.
     * Mirrors: AdminCoursesPage in admin/courses/page.tsx
     */
    public function index(Request $request)
    {
        $query = Course::with('instructor')
            ->withCount('enrollments')
            ->latest();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by program
        if ($program = $request->input('program')) {
            $query->where('program', $program);
        }

        // Filter by publish status
        if ($request->filled('published')) {
            $query->where('is_published', (bool) $request->input('published'));
        }

        // Filter by instructor
        if ($instructorId = $request->input('instructor_id')) {
            $query->where('instructor_id', $instructorId);
        }

        $courses = $query->paginate(30)->withQueryString();

        // Stats
        $stats = [
            'total'       => Course::count(),
            'published'   => Course::where('is_published', true)->count(),
            'draft'       => Course::where('is_published', false)->count(),
            'enrollments' => Enrollment::count(),
        ];

        // Instructor list for filter dropdown
        $instructors = User::where('role', 'instructor')
            ->orderBy('name')
            ->get(['id', 'name']);

        $programs = ['Film Production', 'Digital Media', 'Game Design', 'Audio Engineering'];

        return view('admin.courses.index', compact(
            'courses',
            'stats',
            'instructors',
            'programs',
        ));
    }

    // ── Show / Course-level Admin Panel ───────────────────────────────────────

    /**
     * GET /admin/courses/{course}
     *
     * Course-level admin panel.
     * Mirrors: app/(dashboard)/courses/[courseId]/admin/page.tsx
     */
    public function show(Course $course)
    {
        $course->load([
            'instructor',
            'modules.lessons',
            'enrollments.student',
            'assignments.submissions.grade',
            'liveSessions',
        ]);

        $course->loadCount(['enrollments', 'assignments', 'liveSessions']);

        // Submission stats
        $totalSubmissions = $course->assignments
            ->flatMap->submissions->count();

        $gradedSubmissions = $course->assignments
            ->flatMap->submissions
            ->filter(fn($s) => $s->status === 'graded')->count();

        $avgScore = $course->assignments
            ->flatMap->submissions
            ->flatMap->grade
            ->filter()
            ->avg('score');

        // Available instructors for reassignment
        $instructors = User::where('role', 'instructor')
            ->where('is_verified', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.courses.show', compact(
            'course',
            'totalSubmissions',
            'gradedSubmissions',
            'avgScore',
            'instructors',
        ));
    }

    // ── Toggle Publish ────────────────────────────────────────────────────────

    /**
     * POST /admin/courses/{course}/toggle-publish
     *
     * Flip the is_published flag on any course.
     */
    public function togglePublish(Course $course)
    {
        $course->update(['is_published' => !$course->is_published]);

        $status = $course->is_published ? 'published' : 'unpublished';

        return back()->with('success', "\"{$course->title}\" has been {$status}.");
    }

    // ── Reassign Instructor ───────────────────────────────────────────────────

    /**
     * PUT /admin/courses/{course}/instructor
     *
     * Change which instructor owns the course.
     */
    public function reassignInstructor(Request $request, Course $course)
    {
        $data = $request->validate([
            'instructor_id' => ['required', 'exists:users,id'],
        ]);

        $instructor = User::findOrFail($data['instructor_id']);

        if ($instructor->role === 'student') {
            return back()->with('error', 'Cannot assign a student as instructor.');
        }

        $course->update(['instructor_id' => $data['instructor_id']]);

        return back()->with('success', "Instructor changed to {$instructor->name}.");
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    /**
     * DELETE /admin/courses/{course}
     *
     * Hard-delete a course. Cascades to modules, lessons,
     * assignments, submissions, grades, enrollments.
     */
    public function destroy(Course $course)
    {
        $title = $course->title;
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', "\"{$title}\" and all its content has been deleted.");
    }

    // ── Bulk Actions ──────────────────────────────────────────────────────────

    /**
     * POST /admin/courses/bulk
     *
     * Bulk publish, unpublish, or delete selected courses.
     */
    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action'     => ['required', Rule::in(['publish', 'unpublish', 'delete'])],
            'course_ids' => ['required', 'array'],
            'course_ids.*' => ['exists:courses,id'],
        ]);

        $courses = Course::whereIn('id', $data['course_ids']);

        match ($data['action']) {
            'publish'   => $courses->update(['is_published' => true]),
            'unpublish' => $courses->update(['is_published' => false]),
            'delete'    => $courses->each->delete(),
        };

        $count = count($data['course_ids']);

        return back()->with('success', "{$count} course(s) {$data['action']}ed.");
    }
}
