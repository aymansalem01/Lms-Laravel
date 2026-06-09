<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseAttendance;
use App\Models\AttendanceWarning;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Course $course)
    {
        $students = $course->students()->orderBy('name')->get();
        $records = $course->attendance()->with('student')->get()->groupBy('date')->map(function ($items) {
            return $items->keyBy('student_id')->map->status;
        });
        $dates = $course->attendance()->select('date')->distinct()->orderBy('date', 'desc')->get()->pluck('date');

        return view('courses.attendance.index', compact('course', 'students', 'records', 'dates'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
        ]);

        CourseAttendance::updateOrCreate(
            [
                'course_id' => $course->id,
                'student_id' => $data['student_id'],
                'date' => $data['date'],
            ],
            ['status' => $data['status']]
        );

        return redirect()->route('courses.attendance.index', $course)
            ->with('success', 'Attendance recorded.');
    }

    public function bulkStore(Request $request, Course $course)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:users,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
        ]);

        foreach ($data['attendance'] as $entry) {
            CourseAttendance::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'student_id' => $entry['student_id'],
                    'date' => $data['date'],
                ],
                ['status' => $entry['status']]
            );
        }

        return redirect()->route('courses.attendance.index', $course)
            ->with('success', 'Attendance saved.');
    }

    private function attendanceStats(Course $course, ?int $studentId = null)
    {
        $query = $course->attendance();
        if ($studentId) {
            $query->where('student_id', $studentId);
        }
        $all = $query->get();

        $total = $all->count();
        $present = $all->where('status', 'present')->count();
        $absent = $all->where('status', 'absent')->count();
        $late = $all->where('status', 'late')->count();
        $excused = $all->where('status', 'excused')->count();

        $attended = $present; // only "present" counts as attended
        $absenceRate = $total > 0 ? round(($absent / $total) * 100, 2) : 0;
        $attendanceRate = $total > 0 ? round(($attended / $total) * 100, 2) : 0;

        return compact('total', 'present', 'absent', 'late', 'excused', 'absenceRate', 'attendanceRate');
    }

    public function myAttendance(Course $course)
    {
        $user = auth()->user();
        $records = $course->attendance()->where('student_id', $user->id)->orderBy('date', 'desc')->get();
        $stats = $this->attendanceStats($course, $user->id);
        $warnings = $course->attendanceWarnings()->where('student_id', $user->id)->orderBy('warning_level')->get();

        return view('courses.attendance.student', compact('course', 'records', 'stats', 'warnings'));
    }

    public function report(Course $course)
    {
        $students = $course->students()->orderBy('name')->get();
        $reportData = $students->map(function ($student) use ($course) {
            $stats = $this->attendanceStats($course, $student->id);
            $warnings = $course->attendanceWarnings()->where('student_id', $student->id)->pluck('warning_level');
            return array_merge(['id' => $student->id, 'name' => $student->name, 'email' => $student->email], $stats, ['warnings' => $warnings]);
        });

        $courseWarnings = $course->attendanceWarnings()->with('student')->orderBy('generated_at', 'desc')->get();

        return view('courses.attendance.report', compact('course', 'reportData', 'courseWarnings'));
    }

    public function generateWarnings(Course $course)
    {
        $thresholds = [
            1 => 20, // first warning at >= 20% absence
            2 => 35, // second warning at >= 35% absence
        ];

        $students = $course->students()->get();
        $generated = 0;

        foreach ($students as $student) {
            $stats = $this->attendanceStats($course, $student->id);
            $rate = $stats['absenceRate'];

            foreach ($thresholds as $level => $threshold) {
                if ($rate >= $threshold) {
                    AttendanceWarning::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'student_id' => $student->id,
                            'warning_level' => $level,
                        ],
                        [
                            'absence_rate' => $rate,
                            'generated_at' => now(),
                        ]
                    );
                    $generated++;
                } else {
                    AttendanceWarning::where([
                        'course_id' => $course->id,
                        'student_id' => $student->id,
                        'warning_level' => $level,
                    ])->delete();
                }
            }
        }

        return redirect()->route('courses.attendance.report', $course)
            ->with('success', "Warnings generated/updated for {$generated} entries.");
    }
}
