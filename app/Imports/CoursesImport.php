<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoursesImport
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
                    'title'           => 'required|string|max:255',
                    'description'     => 'nullable|string',
                    'program'         => 'nullable|string|max:255',
                    'course_type'     => 'nullable|string|in:program,sae_core,university',
                    'instructor_email' => 'required|email',
                    'is_published'    => 'nullable|in:yes,no,1,0,true,false',
                ]);

                if ($validator->fails()) {
                    $results['failed']++;
                    $results['errors'][] = "Line {$line}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $instructor = User::where('email', $row['instructor_email'])->where('role', '!=', 'student')->first();
                if (!$instructor) {
                    $results['failed']++;
                    $results['errors'][] = "Line {$line}: Instructor with email '{$row['instructor_email']}' not found";
                    continue;
                }

                $published = in_array(strtolower($row['is_published'] ?? ''), ['yes', '1', 'true'], true);

                Course::create([
                    'title'           => $row['title'],
                    'description'     => $row['description'] ?? null,
                    'program'         => $row['program'] ?? null,
                    'course_type'     => $row['course_type'] ?? 'program',
                    'instructor_id'   => $instructor->id,
                    'is_published'    => $published,
                ]);

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
