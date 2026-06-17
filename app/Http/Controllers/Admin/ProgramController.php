<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Admin ProgramController
 *
 * Mirrors Next.js: app/(dashboard)/admin/programs/page.tsx
 *                  components/admin/ProgramsClient.tsx
 *
 * Responsibilities:
 *  - List all Luminus programs with student/course counts
 *  - Create new programs
 *  - Edit program name/description
 *  - Delete programs (with safety check)
 *  - Assign / unassign courses to programs
 */
class ProgramController extends Controller
{
    // ── Allowed program names (matches DB enum on users table) ────────────────
    const CORE_PROGRAMS = [
        'Film Production',
        'Digital Media',
        'Game Design',
        'Audio Engineering',
    ];

    // ── Index ─────────────────────────────────────────────────────────────────

    /**
     * GET /admin/programs
     *
     * Shows all programs with student count, course count,
     * and the list of courses for assignment.
     * Mirrors: ProgramsPage + ProgramsClient in Next.js
     */
    public function index()
    {
        // Programs with counts
        // students count = users whose program field matches program name
        $programs = Program::orderBy('name')
            ->withCount('courses')
            ->get()
            ->map(function (Program $program) {
                $program->students_count = User::where('program', $program->name)->count();
                return $program;
            });

        // All courses for the assignment panel
        $courses = Course::with('instructor')
            ->withCount('enrollments')
            ->orderBy('title')
            ->get();

        // Summary stats
        $stats = [
            'total_programs' => $programs->count(),
            'total_students' => User::where('role', 'student')->whereNotNull('program')->count(),
            'unassigned'     => User::where('role', 'student')->whereNull('program')->count(),
        ];

        return view('admin.programs.index', compact('programs', 'courses', 'stats'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    /**
     * POST /admin/programs
     *
     * Create a new program.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:programs,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Program::create([
            'name'        => $data['name'],
            'slug'        => str($data['name'])->slug(),
            'description' => $data['description'] ?? null,
        ]);

        return back()->with('success', "Program \"{$data['name']}\" created.");
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    /**
     * GET /admin/programs/{program}
     *
     * Program detail: enrolled students, assigned courses.
     */
    public function show(Program $program)
    {
        $students = User::where('program', $program->name)
            ->where('role', 'student')
            ->latest()
            ->paginate(20);

        $courses = Course::where('program', $program->name)
            ->with('instructor')
            ->withCount('enrollments')
            ->get();

        return view('admin.programs.show', compact('program', 'students', 'courses'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    /**
     * GET /admin/programs/{program}/edit
     */
    public function edit(Program $program)
    {
        $availableCourses = Course::whereNull('program')
            ->orWhere('program', $program->name)
            ->orderBy('title')
            ->get();

        return view('admin.programs.edit', compact('program', 'availableCourses'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    /**
     * PUT /admin/programs/{program}
     *
     * Update program name and description.
     * If name changes, also update users.program and courses.program fields.
     */
    public function update(Request $request, Program $program)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', "unique:programs,name,{$program->id}"],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldName = $program->name;

        $program->update([
            'name'        => $data['name'],
            'slug'        => str($data['name'])->slug(),
            'description' => $data['description'] ?? null,
        ]);

        // Cascade name change to related records
        if ($oldName !== $data['name']) {
            User::where('program', $oldName)->update(['program' => $data['name']]);
            Course::where('program', $oldName)->update(['program' => $data['name']]);
        }

        return redirect()->route('admin.programs.index')
            ->with('success', "Program updated to \"{$data['name']}\".");
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    /**
     * DELETE /admin/programs/{program}
     *
     * Delete a program.
     * Safety: does NOT delete users or courses — sets their program field to null.
     */
    public function destroy(Program $program)
    {
        $name = $program->name;

        // Nullify references before deleting
        User::where('program', $name)->update(['program' => null]);
        Course::where('program', $name)->update(['program' => null]);

        $program->delete();

        return back()->with('success', "Program \"{$name}\" deleted. Users and courses unassigned.");
    }

    // ── Assign Course to Program ──────────────────────────────────────────────

    /**
     * POST /admin/programs/{program}/courses
     *
     * Assign a course to this program by updating courses.program.
     */
    public function assignCourse(Request $request, Program $program)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        $course = Course::findOrFail($data['course_id']);
        $course->update(['program' => $program->name]);

        return back()->with('success', "\"{$course->title}\" assigned to {$program->name}.");
    }

    // ── Unassign Course from Program ──────────────────────────────────────────

    /**
     * DELETE /admin/programs/{program}/courses/{course}
     *
     * Remove a course from this program.
     */
    public function unassignCourse(Program $program, Course $course)
    {
        $course->update(['program' => null]);

        return back()->with('success', "\"{$course->title}\" removed from {$program->name}.");
    }
}
