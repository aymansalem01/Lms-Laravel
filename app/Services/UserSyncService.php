<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSyncService
{
    /**
     * Create or update a user on the LMS from SIS_CRM sync data.
     */
    public function upsertUser(array $data): ?User
    {
        $email = $data['email'];
        $name = $data['name'];
        $passwordHash = $data['password_hash'];
        $role = $data['role'] ?? 'student';
        $sisCrmUserId = $data['sis_crm_user_id'] ?? null;
        $sisStudentId = $data['sis_student_id'] ?? null;
        $ssoProvider = $data['sso_provider'] ?? 'sis_crm';

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'name' => $name,
                'password' => $passwordHash,
                'sis_crm_user_id' => $sisCrmUserId ?? $user->sis_crm_user_id,
                'sso_provider' => $ssoProvider,
            ]);

            return $user;
        }

        return User::create([
            'email' => $email,
            'name' => $name,
            'password' => $passwordHash,
            'role' => $role,
            'sis_crm_user_id' => $sisCrmUserId,
            'sso_provider' => $ssoProvider,
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Deactivate a user on the LMS (called by SIS_CRM).
     */
    public function deactivateUser(int $sisCrmUserId): bool
    {
        $user = User::where('sis_crm_user_id', $sisCrmUserId)->first();

        if (! $user) {
            return false;
        }

        $user->update(['is_verified' => false]);

        return true;
    }
}
