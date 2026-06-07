<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\Grade;
use App\Models\LiveSession;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

/**
 * Admin AnalyticsController
 *
 * Mirrors Next.js: app/(dashboard)/admin/analytics/page.tsx
 *
 * Responsibilities:
 *  - Platform-wide stats (users, courses, submissions, grades)
 *  - Students breakdown by program (bar chart data)
 *  - Top courses by enrollment
 *  - Grading completion rate
 *  - Monthly signups (chart data)
 *  - Submission trends
 */
class AnalyticsController extends Controller
{
    /**
     * GET /admin/analytics
     *
     * Loads all platform metrics and passes them to the view.
     * Mirrors: AdminAnalyticsPage in analytics/page.tsx
     */
    public function index()
    {
        // ── Core counts (mirrors Promise.all in Next.js) ──────────────────────
        $totalUsers        = User::count();
        $totalStudents     = User::where('role', 'student')->count();
        $totalInstructors  = User::where('role', 'instructor')->count();
        $totalAdmins       = User::where('role', 'admin')->count();
        $totalCourses      = Course::count();
        $publishedCourses  = Course::where('is_published', true)->count();
        $draftCourses      = Course::where('is_published', false)->count();
        $totalEnrollments  = Enrollment::count();
        $totalSubmissions  = Submission::count();
        $gradedSubmissions = Submission::where('status', 'graded')->count();
        $totalGrades       = Grade::count();
        $totalLiveSessions = LiveSession::count();

        // Grading completion rate (%)
        $gradingRate = $totalSubmissions > 0
            ? round(($gradedSubmissions / $totalSubmissions) * 100)
            : 0;

        // Average score across all grades
        $avgScore = Grade::avg('score') ?? 0;

        // ── Students by program ───────────────────────────────────────────────
        // Mirrors: programData breakdown in Next.js analytics page
        $programCounts = User::where('role', 'student')
            ->whereNotNull('program')
            ->select('program', DB::raw('COUNT(*) as count'))
            ->groupBy('program')
            ->orderByDesc('count')
            ->pluck('count', 'program')
            ->toArray();

        $programTotal = array_sum($programCounts);

        // Add percentage to each program
        $programBreakdown = collect($programCounts)->map(function ($count) use ($programTotal) {
            return [
                'count'   => $count,
                'percent' => $programTotal > 0 ? round(($count / $programTotal) * 100) : 0,
            ];
        });

        // ── Top courses by enrollment ─────────────────────────────────────────
        // Mirrors: coursesByEnrollment in Next.js analytics page
        $topCourses = Course::with('instructor')
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(10)
            ->get();

        // ── Monthly signups (current year) ────────────────────────────────────
        $monthlySignups = User::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        $signupsByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $signupsByMonth[$m] = $monthlySignups[$m] ?? 0;
        }

        // ── Monthly submissions (current year) ────────────────────────────────
        $monthlySubmissions = Submission::select(
                DB::raw('MONTH(submitted_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('submitted_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $submissionsByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $submissionsByMonth[$m] = $monthlySubmissions[$m] ?? 0;
        }

        // ── Enrollments per course (top 5 for mini chart) ─────────────────────
        $enrollmentsByProgram = Course::select('program', DB::raw('COUNT(*) as count'))
            ->whereNotNull('program')
            ->groupBy('program')
            ->pluck('count', 'program')
            ->toArray();

        // ── Recent activity feed ──────────────────────────────────────────────
        $recentEnrollments = Enrollment::with(['student', 'course'])
            ->latest('enrolled_at')
            ->limit(5)
            ->get();

        $recentGrades = Grade::with(['submission.student', 'submission.assignment.course', 'instructor'])
            ->latest('graded_at')
            ->limit(5)
            ->get();

        $recentUsers = User::latest()->limit(5)->get();

        // Month names for chart labels
        $monthNames = [
            1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'May', 6=>'Jun',
            7=>'Jul', 8=>'Aug', 9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dec',
        ];

        return view('admin.analytics.index', compact(
            // Core counts
            'totalUsers',
            'totalStudents',
            'totalInstructors',
            'totalAdmins',
            'totalCourses',
            'publishedCourses',
            'draftCourses',
            'totalEnrollments',
            'totalSubmissions',
            'gradedSubmissions',
            'totalGrades',
            'totalLiveSessions',

            // Rates & averages
            'gradingRate',
            'avgScore',

            // Breakdown data
            'programBreakdown',
            'programTotal',
            'enrollmentsByProgram',

            // Charts
            'topCourses',
            'signupsByMonth',
            'submissionsByMonth',
            'monthNames',

            // Recent activity
            'recentEnrollments',
            'recentGrades',
            'recentUsers',
        ));
    }
}
