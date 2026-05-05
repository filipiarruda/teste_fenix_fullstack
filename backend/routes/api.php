<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StudentExamController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/health', fn() => response()->json(['status' => 'ok', 'timestamp' => now()]));

    // Auth público
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login',    [AuthController::class, 'login']);

    // Auth protegido
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me',      [AuthController::class, 'me']);

        // Exams (CRUD para professores)
        Route::apiResource('exams', ExamController::class);

        // Student Exams (Responder provas)
        Route::get('/student/exams', [StudentExamController::class, 'index']);
        Route::get('/student/exams/{exam}', [StudentExamController::class, 'show']);
        Route::post('/student/exams/{exam}/submit', [StudentExamController::class, 'submit']);
        Route::get('/student/exams/{exam}/result', [StudentExamController::class, 'result']);
    });
});

