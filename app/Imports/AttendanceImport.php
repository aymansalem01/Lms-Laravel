<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\CourseAttendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceImport
{
    public function import(Course $course, array $rows): array
    {
        $results = [
            'total' => count($rows),
            'succeeded' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $studentEmails = $course->students()->pluck('email', 'id')->flip();

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $line = $index + 2;

                $validator = Validator::make($row, [
                    'student_email' => 'required|email',
                    'date' => 'required|date',
                    'status' => 'required|in:present,absent,late,excused',
                ]);

                if ($validator->fails()) {
                    $results['failed']++;
                    $results['errors'][] = "Line {$line}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $student = User::where('email', $row['student_email'])->first();
                if (!$student) {
                    $results['failed']++;
                    $results['errors'][] = "Line {$line}: Student with email '{$row['student_email']}' not found";
                    continue;
                }

                if (!$course->students()->where('user_id', $student->id)->exists()) {
                    $results['failed']++;
                    $results['errors'][] = "Line {$line}: '{$row['student_email']}' is not enrolled in this course";
                    continue;
                }

                CourseAttendance::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'student_id' => $student->id,
                        'date' => $row['date'],
                    ],
                    ['status' => $row['status']]
                );

                $results['succeeded']++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }
}
