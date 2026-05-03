<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;

Route::get("/test", function() {
    return response()->json(["status" => "ok", "test" => true]);
});

Route::get("/api-docs.json", function() {
    return response()->file(storage_path("api-docs/api-docs.json"));
});

Route::get("/docs", function() {
    return response()->file(storage_path("api-docs/api-docs.json"));
});

Route::prefix("v1")->group(function(){
  Route::get("/health", [HealthController::class, "check"]);

  Route::post("/auth/register", [AuthController::class, "register"]);
  Route::post("/auth/login", [AuthController::class, "login"]);

  Route::middleware("auth:sanctum")->group(function(){
    Route::post("/auth/logout", [AuthController::class, "logout"]);
    Route::get("/auth/me", [AuthController::class, "me"]);

    Route::apiResource("/exams", ExamController::class);

    Route::get("/exams/{exam}/questions", [QuestionController::class, "index"]);
    Route::post("/exams/{exam}/questions", [QuestionController::class, "store"]);
    Route::put("/questions/{question}", [QuestionController::class, "update"]);
    Route::delete("/questions/{question}", [QuestionController::class, "destroy"]);

    Route::post("/submissions/submit", [SubmissionController::class, "submit"]);
    Route::get("/submissions/exam/{examId}/result/{userId}", [SubmissionController::class, "getResult"]);
    Route::get("/submissions/exam/{examId}/user/{userId}", [SubmissionController::class, "getUserAnswers"]);

    Route::get("/analytics/exam/{exam}", [AnalyticsController::class, "show"]);
    Route::get("/analytics/exam/{exam}/ranking", [AnalyticsController::class, "ranking"]);
    Route::get("/analytics/exam/{exam}/average", [AnalyticsController::class, "average"]);
    Route::get("/analytics/exam/{exam}/top", [AnalyticsController::class, "top"]);
  });
});
