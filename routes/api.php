<?php

use App\Http\Controllers\Api\SsoController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Health check
    Route::get('/health', fn () => response()->json(['status' => 'ok']));

    // ---------------------------------------------------------------
    // Internal sync endpoints (called by SIS_CRM with API token)
    // ---------------------------------------------------------------
    Route::prefix('sync')->middleware('api-secret')->group(function () {
        Route::post('/user', [SyncController::class, 'upsertUser']);
        Route::delete('/user/{id}', [SyncController::class, 'deactivateUser']);
    });

    // ---------------------------------------------------------------
    // SSO endpoints
    // ---------------------------------------------------------------
    Route::prefix('sso')->group(function () {
        // Called by SIS_CRM to validate an SSO token
        Route::post('/validate', [SsoController::class, 'validateToken'])
            ->middleware('api-secret');

        // Called by LMS session to generate an SSO redirect
        Route::get('/authorize', [SsoController::class, 'authorize'])
            ->middleware('web', 'auth');

        // Called by SIS_CRM after receiving the SSO token
        Route::get('/callback', [SsoController::class, 'callback'])
            ->middleware('web');
    });

    // ---------------------------------------------------------------
    // LMS data endpoints (consumed by SIS_CRM)
    // ---------------------------------------------------------------
    Route::middleware('api-secret')->group(function () {
        Route::get('/courses/{id}/grades', [SyncController::class, 'courseGrades']);
        Route::get('/courses/{id}/attendance', [SyncController::class, 'courseAttendance']);
    });
});
