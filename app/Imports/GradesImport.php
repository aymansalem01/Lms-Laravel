<?php

namespace App\Imports;

use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GradesImport
{
    public function import(array $rows): array
    {
        $results = [
            'total' => count($rows),
            'succeeded' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $line = $index + 2;

                $validator = Validator::make($row, [
                    'student_email' => 'required|email',
                    'assignment_title' => 'required|string',
                    'score' => 'required|numeric|min:0',
                    'feedback' => 'nullable|string',
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

                $assignment = Assignment::where('title', $row['assignment_title'])->first();
                if (!$assignment) {
                    $results['failed']++;
                    $results['errors'][] = "Line {$line}: Assignment titled '{$row['assignment_title']}' not found";
                    continue;
                }

                $submission = Submission::firstOrCreate(
                    [
                        'assignment_id' => $assignment->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'status' => 'submitted',
                        'submitted_at' => now(),
                    ]
                );

                Grade::updateOrCreate(
                    ['submission_id' => $submission->id],
                    [
                        'score' => $row['score'],
                        'feedback' => $row['feedback'] ?? null,
                        'instructor_id' => Auth::id(),
                        'graded_at' => now(),
                    ]
                );

                $submission->update(['status' => 'graded']);

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
