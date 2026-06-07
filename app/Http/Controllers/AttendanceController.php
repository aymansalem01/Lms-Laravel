<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseAttendance;
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
}
