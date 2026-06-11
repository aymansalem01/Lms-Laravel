<?php

namespace App\Imports;

use App\Models\QuestionBank;
use App\Models\QuestionBankItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuizQuestionsImport
{
    public function import(QuestionBank $bank, array $rows): array
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
                    'type' => 'required|in:multiple_choice,true_false,short_answer,long_answer',
                    'question' => 'required|string',
                    'options' => 'nullable|string',
                    'correct_answer' => 'nullable|string',
                    'points' => 'required|integer|min:1',
                ]);

                if ($validator->fails()) {
                    $results['failed']++;
                    $results['errors'][] = "Line {$line}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $type = $row['type'];
                $options = null;

                if ($type === 'multiple_choice') {
                    $options = array_map('trim', explode('|', $row['options'] ?? ''));
                    $options = array_values(array_filter($options));
                    if (empty($options)) {
                        $results['failed']++;
                        $results['errors'][] = "Line {$line}: multiple_choice requires pipe-delimited options column (e.g. 'Option A|Option B|Option C')";
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

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }
}
