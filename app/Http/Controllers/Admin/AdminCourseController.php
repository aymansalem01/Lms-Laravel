<?php

namespace App\Http\Controllers\Admin;

use App\Imports\CoursesImport;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    // ── Create ─────────────────────────────────────────────────────────────────

    public function create()
    {
        $instructors = User::where('role', 'instructor')->orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.courses.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'program'     => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'course_type'  => 'nullable|string|in:program,sae_core,university',
            'is_published' => 'boolean',
            'instructor_id' => 'required|exists:users,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image_url'] = Storage::url($request->file('cover_image')->store('courses', 'public'));
        }

        unset($data['cover_image']);

        Course::create($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    // ── Edit ───────────────────────────────────────────────────────────────────

    public function edit(Course $course)
    {
        $instructors = User::where('role', 'instructor')->orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.courses.edit', compact('course', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'program'     => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'course_type'  => 'nullable|string|in:program,sae_core,university',
            'status'      => 'nullable|in:draft,published',
        ]);

        if ($request->filled('status')) {
            $data['is_published'] = $request->status === 'published';
        }

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image_url) {
                $oldPath = str_replace(Storage::url(''), '', $course->cover_image_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $data['cover_image_url'] = Storage::url($request->file('cover_image')->store('courses', 'public'));
        }

        unset($data['cover_image']);

        $course->update($data);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course updated successfully.');
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

    public function bulkCreate(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $rows = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));
        $header = array_map('trim', array_shift($rows));
        $data = array_map(fn($row) => array_combine($header, array_map('trim', $row)), $rows);

        $results = app(CoursesImport::class)->import($data);

        $total = $results['succeeded'] + $results['failed'];
        $message = "{$results['succeeded']} of {$total} courses created.";

        if ($results['failed'] > 0) {
            return redirect()->route('admin.courses.index')
                ->with('warning', $message)
                ->with('import_errors', $results['errors']);
        }

        return redirect()->route('admin.courses.index')->with('success', $message);
    }

    public function downloadBulkExample()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="courses-bulk-import-example.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['title', 'description', 'program', 'course_type', 'instructor_email', 'is_published']);
            fputcsv($handle, ['Introduction to Film', 'A beginner course in film production.', 'Film Production', 'program', 'instructor@luminus.jo', '1']);
            fputcsv($handle, ['Digital Media Design', 'Principles of digital media and design.', 'Digital Media', 'program', 'instructor@luminus.jo', '1']);
            fputcsv($handle, ['Game Development 101', 'Learn the basics of game development.', 'Game Design', 'sae_core', 'instructor@luminus.jo', '0']);
            fputcsv($handle, ['Audio Mixing', 'Advanced audio mixing techniques.', 'Audio Engineering', 'university', 'instructor@luminus.jo', '1']);
            fclose($handle);
        };

        return new \Symfony\Component\HttpFoundation\StreamedResponse($callback, 200, $headers);
    }
}
