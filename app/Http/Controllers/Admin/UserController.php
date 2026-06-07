<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Admin UserController
 *
 * Mirrors Next.js: app/(dashboard)/admin/users/page.tsx
 * Mirrors actions: app/actions/admin.ts
 *
 * Responsibilities:
 *  - List all users (students, instructors, admins) with stats
 *  - Show pending instructor verifications
 *  - Change user roles
 *  - Verify / revoke instructor credentials
 *  - Invite new users via email
 *  - Enroll a user into a course manually
 *  - Show individual user profile
 */
class UserController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    /**
     * GET /admin/users
     *
     * Loads all users with their stats, groups them by role,
     * and surfaces instructors pending verification.
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->withCount(['enrollments as enrolled_courses_count'])
            ->withCount(['submissions'])
            ->latest();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Program filter
        if ($program = $request->input('program')) {
            $query->where('program', $program);
        }

        $users = $query->paginate(30)->withQueryString();

        // Stats (always across all users, not filtered)
        $stats = [
            'total'       => User::count(),
            'students'    => User::where('role', 'student')->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'admins'      => User::where('role', 'admin')->count(),
            'pending'     => User::where('role', 'instructor')->where('is_verified', false)->count(),
        ];

        // Pending verification panel (instructors not yet verified)
        $pendingVerification = User::where('role', 'instructor')
            ->where('is_verified', false)
            ->latest()
            ->get();

        // Courses list — for the "enroll user" modal
        $courses = Course::orderBy('title')->get(['id', 'title', 'program']);

        $programs = ['Film Production', 'Digital Media', 'Game Design', 'Audio Engineering'];

        return view('admin.users.index', compact(
            'users',
            'stats',
            'pendingVerification',
            'courses',
            'programs',
        ));
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    /**
     * GET /admin/users/{user}
     *
     * Full profile detail: enrollments, submissions, grades, portfolio.
     */
    public function show(User $user)
    {
        $user->load([
            'enrolledCourses.instructor',
            'submissions.assignment.course',
            'submissions.grade',
            'portfolioItems',
        ]);

        $courses = Course::orderBy('title')->get(['id', 'title', 'program']);

        return view('admin.users.show', compact('user', 'courses'));
    }

    // ── Store (manual create) ────────────────────────────────────────────────

    /**
     * POST /admin/users
     *
     * Manually create a new user with full details.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', Rule::in(['student', 'instructor', 'admin'])],
            'program'  => ['nullable', 'string', 'max:255'],
            'bio'      => ['nullable', 'string', 'max:2000'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'program'  => $data['program'] ?? null,
            'bio'      => $data['bio'] ?? null,
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "User {$user->name} created successfully.");
    }

    // ── Update profile ───────────────────────────────────────────────────────

    /**
     * PUT /admin/users/{user}
     *
     * Update user name, email, program, bio.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'program' => ['nullable', 'string', 'max:255'],
            'bio'     => ['nullable', 'string', 'max:2000'],
        ]);

        $user->update($data);

        return back()->with('success', "Profile updated for {$user->name}.");
    }

    // ── Update password ──────────────────────────────────────────────────────

    /**
     * POST /admin/users/{user}/password
     *
     * Force-set a new password for the user.
     */
    public function updatePassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed:password_confirmation'],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password updated successfully.');
    }

    // ── Role Management ───────────────────────────────────────────────────────

    /**
     * PUT /admin/users/{user}/role
     *
     * Change a user's role (student | instructor | admin).
     * Mirrors: changeUserRole() in actions/admin.ts
     */
    public function updateRole(Request $request, User $user)
    {
        // Prevent admin from demoting themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $data = $request->validate([
            'role' => ['required', Rule::in(['student', 'instructor', 'admin'])],
        ]);

        $user->update(['role' => $data['role']]);

        return back()->with('success', "Role updated to {$data['role']} for {$user->name}.");
    }

    // ── Instructor Verification ───────────────────────────────────────────────

    /**
     * POST /admin/users/{user}/verify
     *
     * Mark an instructor as verified.
     * Mirrors: verifyInstructor() in actions/admin.ts
     */
    public function verifyInstructor(User $user)
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'User is not an instructor.');
        }

        $user->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return back()->with('success', "{$user->name} has been verified as an instructor.");
    }

    /**
     * POST /admin/users/{user}/revoke
     *
     * Revoke instructor verification.
     * Mirrors: revokeVerification() in actions/admin.ts
     */
    public function revokeVerification(User $user)
    {
        $user->update([
            'is_verified'  => false,
            'verified_at'  => null,
            'verified_by'  => null,
        ]);

        return back()->with('success', "Verification revoked for {$user->name}.");
    }

    // ── Invite ────────────────────────────────────────────────────────────────

    /**
     * POST /admin/users/invite
     *
     * Send an invite email to a new user.
     * Mirrors: createUserInvite() in actions/admin.ts
     *
     * In production: queue a signed URL email via Laravel Mail + Resend.
     * Here we create the user with a random password and show credentials.
     */
    public function invite(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'role'  => ['required', Rule::in(['student', 'instructor', 'admin'])],
            'name'  => ['nullable', 'string', 'max:255'],
        ]);

        $tempPassword = str()->random(12);

        $user = User::create([
            'name'     => $data['name'] ?? explode('@', $data['email'])[0],
            'email'    => strtolower(trim($data['email'])),
            'role'     => $data['role'],
            'password' => Hash::make($tempPassword),
        ]);

        // TODO: Mail::to($user)->send(new UserInviteMail($user, $tempPassword));

        return back()->with('success',
            "Invited {$user->email} as {$data['role']}. Temp password: {$tempPassword}"
        );
    }

    // ── Manual Enrollment ─────────────────────────────────────────────────────

    /**
     * POST /admin/users/{user}/enroll
     *
     * Enroll a user into a course manually (admin override).
     * Mirrors: enrollUserInCourse() in actions/admin.ts
     */
    public function enrollInCourse(Request $request, User $user)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        Enrollment::firstOrCreate([
            'student_id' => $user->id,
            'course_id'  => $data['course_id'],
        ]);

        $course = Course::find($data['course_id']);

        return back()->with('success', "{$user->name} enrolled in {$course->title}.");
    }

    // ── Unenroll ──────────────────────────────────────────────────────────────

    /**
     * DELETE /admin/users/{user}/enroll/{course}
     *
     * Remove a user from a course.
     */
    public function unenrollFromCourse(User $user, Course $course)
    {
        Enrollment::where([
            'student_id' => $user->id,
            'course_id'  => $course->id,
        ])->delete();

        return back()->with('success', "{$user->name} removed from {$course->title}.");
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    /**
     * DELETE /admin/users/{user}
     *
     * Hard-delete a user. Cascades to enrollments, submissions, etc.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "{$name} has been deleted.");
    }
}
