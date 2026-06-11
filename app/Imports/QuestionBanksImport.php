<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\QuestionBank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionBanksImport
{
    public function import(array $rows): array
    {
        $results = [
            'total' => count($rows),
            'succeeded' => 0,
            'failed' => 0,
            'banks_created' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            $grouped = collect($rows)->groupBy(fn($r) => $r['bank_name'] ?? 'Untitled Bank');

            foreach ($grouped as $bankName => $bankRows) {
                $bankName = trim($bankName);

                $bankValidator = Validator::make(['bank_name' => $bankName], [
                    'bank_name' => 'required|string|max:255',
                ]);

                if ($bankValidator->fails()) {
                    $results['failed'] += $bankRows->count();
                    $results['errors'][] = "Bank \"{$bankName}\": " . implode(', ', $bankValidator->errors()->all());
                    continue;
                }

                $firstRow = $bankRows->first();

                $courseIds = null;
                if (!empty($firstRow['course_ids'])) {
                    $courseIds = array_map('trim', explode('|', $firstRow['course_ids']));
                    $courseIds = array_filter($courseIds);
                    $validCourses = Course::whereIn('id', $courseIds)->pluck('id')->toArray();
                    $invalid = array_diff($courseIds, $validCourses);
                    if (!empty($invalid)) {
                        $results['failed'] += $bankRows->count();
                        $results['errors'][] = "Bank \"{$bankName}\": invalid course IDs: " . implode(', ', $invalid);
                        continue;
                    }
                }

                $bank = QuestionBank::create([
                    'name'              => $bankName,
                    'user_id'           => Auth::id(),
                    'is_visible_to_all' => empty($courseIds),
                ]);

                if (!empty($courseIds)) {
                    $bank->courses()->attach($courseIds);
                }

                $results['banks_created']++;

                foreach ($bankRows as $index => $row) {
                    $line = $index + 2;

                    $validator = Validator::make($row, [
                        'type' => 'required|in:multiple_choice,true_false,short_answer,long_answer',
                        'question' => 'required|string',
                        'options' => 'nullable|string',
                        'correct_answer' => 'nullable|string',
                        'points' => 'required|integer|min:1',
                    ]);

                    if ($validator->fails()) {
                        $results['failed']++;
                        $results['errors'][] = "Line {$line} (\"{$bankName}\"): " . implode(', ', $validator->errors()->all());
                        continue;
                    }

                    $type = $row['type'];
                    $options = null;

                    if ($type === 'multiple_choice') {
                        $options = array_map('trim', explode('|', $row['options'] ?? ''));
                        $options = array_values(array_filter($options));
                        if (empty($options)) {
                            $results['failed']++;
                            $results['errors'][] = "Line {$line} (\"{$bankName}\"): multiple_choice requires pipe-delimited options";
                            continue;
                        }
                    }

                    $bank->items()->create([
                        'user_id' => Auth::id(),
                        'type' => $type,
                        'question' => $row['question'],
                        'options' => $options,
                        'correct_answer' => $row['correct_answer'] ?? null,
                        'points' => (int) $row['points'],
                    ]);

                    $results['succeeded']++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }
}
