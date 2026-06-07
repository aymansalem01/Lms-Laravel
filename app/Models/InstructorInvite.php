<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorInvite extends Model
{
    protected $fillable = ['email', 'token', 'role', 'invited_by', 'expires_at'];

    protected function casts(): array
    {
        return [
            'used' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
