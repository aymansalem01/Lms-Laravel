<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersImport
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
                    'name'     => 'required|string|max:255',
                    'email'    => 'required|email|unique:users,email',
                    'role'     => 'required|in:student,instructor,admin',
                    'password' => 'nullable|string|min:8',
                    'program'  => 'nullable|string|max:255',
                    'bio'      => 'nullable|string|max:2000',
                ]);

                if ($validator->fails()) {
                    $results['failed']++;
                    $results['errors'][] = "Line {$line}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                User::create([
                    'name'     => $row['name'],
                    'email'    => strtolower(trim($row['email'])),
                    'role'     => $row['role'],
                    'password' => Hash::make($row['password'] ?? 'password'),
                    'program'  => $row['program'] ?? null,
                    'bio'      => $row['bio'] ?? null,
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
