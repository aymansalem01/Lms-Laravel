<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\Announcement;
use App\Models\AnnouncementDismissal;
use App\Models\LiveSession;
use App\Models\Quiz;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $announcements = Announcement::with('author')
            ->whereDoesntHave('dismissals', fn($q) => $q->where('user_id', $user->id))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $data = compact('announcements');

        if ($user->isAdmin()) {
            $data['totalUsers'] = User::count();
            $data['totalCourses'] = Course::count();
            $data['totalSubmissions'] = Submission::count();
            $data['pendingGrades'] = Submission::where('status', 'submitted')
                ->whereDoesntHave('grade')->count();
            $sessions = LiveSession::with('course')
                ->where('scheduled_at', '>=', now())
                ->get()->map(fn($s) => [
                    'id'          => $s->id,
                    'type'        => 'live_session',
                    'title'       => $s->title,
                    'course_id'   => $s->course_id,
                    'course_title'=> $s->course->title ?? '',
                    'date'        => $s->scheduled_at,
                    'label'       => 'Session',
                    'route'       => route('courses.live.show', ['course' => $s->course_id, 'session' => $s->id]),
                ]);
            $assignments = Assignment::with('course')
                ->where('due_date', '>=', now())
                ->get()->map(fn($a) => [
                    'id'          => $a->id,
                    'type'        => 'assignment',
                    'title'       => $a->title,
                    'course_id'   => $a->course_id,
                    'course_title'=> $a->course->title ?? '',
                    'date'        => $a->due_date,
                    'label'       => 'Due',
                    'route'       => route('courses.assignments.show', ['course' => $a->course_id, 'assignment' => $a->id]),
                ]);
            $data['upcomingEvents'] = collect(array_merge($sessions->toArray(), $assignments->toArray()))
                ->sortBy('date')->take(20)->values();
        } elseif ($user->isInstructor()) {
            $courses = $user->taughtCourses()->withCount('enrollments')->get();
            $courseIds = $courses->pluck('id');
            $data['myCourses'] = $courses;
            $data['myCoursesCount'] = $courses->count();
            $data['totalStudents'] = $courses->sum('enrollments_count');
            $data['pendingGrading'] = Submission::whereIn('assignment_id', function ($q) use ($courseIds) {
                $q->select('id')->from('assignments')->whereIn('course_id', $courseIds);
            })->where('status', 'submitted')->whereDoesntHave('grade')->count();
            $data['recentSubmissions'] = Submission::with('assignment', 'student')
                ->whereIn('assignment_id', function ($q) use ($courseIds) {
                    $q->select('id')->from('assignments')->whereIn('course_id', $courseIds);
                })->latest()->take(10)->get();
            $instructorSessions = LiveSession::with('course')
                ->whereIn('course_id', $courseIds)
                ->where('scheduled_at', '>=', now())
                ->get()->map(fn($s) => [
                    'id'          => $s->id,
                    'type'        => 'live_session',
                    'title'       => $s->title,
                    'course_id'   => $s->course_id,
                    'course_title'=> $s->course->title ?? '',
                    'date'        => $s->scheduled_at,
                    'label'       => 'Session',
                    'route'       => route('courses.live.show', ['course' => $s->course_id, 'session' => $s->id]),
                ]);
            $instructorAssignments = Assignment::with('course')
                ->whereIn('course_id', $courseIds)
                ->where('due_date', '>=', now())
                ->get()->map(fn($a) => [
                    'id'          => $a->id,
                    'type'        => 'assignment',
                    'title'       => $a->title,
                    'course_id'   => $a->course_id,
                    'course_title'=> $a->course->title ?? '',
                    'date'        => $a->due_date,
                    'label'       => 'Due',
                    'route'       => route('courses.assignments.show', ['course' => $a->course_id, 'assignment' => $a->id]),
                ]);
            $data['upcomingEvents'] = collect(array_merge($instructorSessions->toArray(), $instructorAssignments->toArray()))
                ->sortBy('date')->take(20)->values();
        } else {
            $enrolledCourseIds = $user->enrollments()->pluck('course_id');
            $data['enrolledCourses'] = Course::whereIn('id', $enrolledCourseIds)->with('instructor')->get();
            $studentAssignments = Assignment::with('course')
                ->whereIn('course_id', $enrolledCourseIds)
                ->where('due_date', '>=', now())
                ->get()->map(fn($a) => [
                    'id'          => $a->id,
                    'type'        => 'assignment',
                    'title'       => $a->title,
                    'course_id'   => $a->course_id,
                    'course_title'=> $a->course->title ?? '',
                    'date'        => $a->due_date,
                    'label'       => 'Due',
                    'route'       => route('courses.assignments.show', ['course' => $a->course_id, 'assignment' => $a->id]),
                ]);
            $studentSessions = LiveSession::with('course')
                ->whereIn('course_id', $enrolledCourseIds)
                ->where('scheduled_at', '>=', now())
                ->get()->map(fn($s) => [
                    'id'          => $s->id,
                    'type'        => 'live_session',
                    'title'       => $s->title,
                    'course_id'   => $s->course_id,
                    'course_title'=> $s->course->title ?? '',
                    'date'        => $s->scheduled_at,
                    'label'       => 'Session',
                    'route'       => route('courses.live.show', ['course' => $s->course_id, 'session' => $s->id]),
                ]);
            $studentQuizzes = Quiz::with('course')
                ->whereIn('course_id', $enrolledCourseIds)
                ->whereDoesntHave('attempts', fn($q) => $q->where('student_id', $user->id))
                ->get()->map(fn($q) => [
                    'id'          => $q->id,
                    'type'        => 'quiz',
                    'title'       => $q->title,
                    'course_id'   => $q->course_id,
                    'course_title'=> $q->course->title ?? '',
                    'date'        => $q->created_at,
                    'label'       => 'Quiz',
                    'route'       => route('courses.quizzes.show', ['course' => $q->course_id, 'quiz' => $q->id]),
                ]);
            $data['upcomingEvents'] = collect(array_merge($studentAssignments->toArray(), $studentSessions->toArray(), $studentQuizzes->toArray()))
                ->sortBy('date')->take(20)->values();
            $data['recentGrades'] = Grade::with('submission.assignment.course')
                ->whereIn('submission_id', fn($q) => $q->select('id')->from('submissions')->where('student_id', $user->id))
                ->latest()->take(10)->get();
        }

        return view('dashboard.index', $data);
    }
}
