<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\ChapterController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminResultController;
use App\Http\Controllers\Api\QuizAttemptController;
use App\Http\Controllers\Api\QuizQuestionController;
use App\Http\Controllers\Api\RankController;
use App\Http\Controllers\Api\SubjectManagementController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// ── Public routes ──
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/send-otp', [OtpController::class, 'send']);
Route::post('/verify-otp', [OtpController::class, 'verify']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/admin/send-otp', [OtpController::class, 'send']);
Route::post('/admin/verify-otp', [OtpController::class, 'verify']);
Route::post('/admin/reset-password', [AdminAuthController::class, 'resetPassword']);

Route::get('/public/boards', [SubjectManagementController::class, 'boards']);
Route::get('/public/boards/{board}/standards', [SubjectManagementController::class, 'standards']);
Route::get('/public/boards/{board}/standards/{standard}/subjects', [SubjectManagementController::class, 'subjectsByStandard']);
Route::get('/public/standards/{standard}/subjects', [SubjectManagementController::class, 'subjectsForStandard']);

// Public read-only taxonomy endpoints (for admin with hardcoded token)
Route::get('/public/taxonomy/boards', [SubjectManagementController::class, 'boards']);
Route::get('/public/taxonomy/boards/{board}/standards', [SubjectManagementController::class, 'standards']);
Route::get('/public/taxonomy/standards/{standard}/subjects', [SubjectManagementController::class, 'subjectsForStandard']);
Route::get('/public/taxonomy/subjects/{subject}/chapters', [ChapterController::class, 'bySubject']);
Route::get('/public/taxonomy/subjects/manage', [SubjectManagementController::class, 'index']);
Route::get('/public/taxonomy/standards', [SubjectManagementController::class, 'allStandards']);
Route::get('/public/taxonomy/chapters', [ChapterController::class, 'index']);
Route::get('/public/taxonomy/questions', [QuestionController::class, 'index']);
Route::get('/public/taxonomy/quizzes', [QuestionController::class, 'getQuizzes']);
Route::get('/public/taxonomy/quizzes/filter', [QuestionController::class, 'getQuizzesByFilter']);

// Public admin read endpoints (dev mode with hardcoded token)
Route::get('/public/admin/users', [UserController::class, 'index']);
Route::delete('/public/admin/users/{id}', [UserController::class, 'destroy']);
Route::get('/public/admin/messages', [ContactController::class, 'index']);
Route::get('/public/admin/stats', [AdminDashboardController::class, 'index']);
Route::get('/public/admin/results', [AdminResultController::class, 'index']);
Route::get('/public/admin/boards', [SubjectManagementController::class, 'boards']);
Route::get('/public/admin/boards/{board}/standards', [SubjectManagementController::class, 'standards']);

// Public admin message endpoints (dev mode with hardcoded token)
Route::delete('/public/admin/messages/{id}', [ContactController::class, 'destroy']);
Route::delete('/public/admin/messages', [ContactController::class, 'destroyAll']);
Route::put('/public/admin/messages/{id}/read', [ContactController::class, 'markAsRead']);
Route::put('/public/admin/messages/read-all', [ContactController::class, 'markAllRead']);

// Public admin write endpoints (dev mode with hardcoded token)
Route::post('/public/admin/questions', [QuestionController::class, 'store']);
Route::put('/public/admin/questions/{id}', [QuestionController::class, 'update']);
Route::delete('/public/admin/questions/{id}', [QuestionController::class, 'destroy']);
Route::post('/public/admin/questions/bulk-delete', [QuestionController::class, 'bulkDelete']);
Route::post('/public/admin/questions/bulk-store', [QuestionController::class, 'bulkStore']);

Route::post('/public/admin/quizzes', [QuestionController::class, 'storeQuiz']);
Route::put('/public/admin/quizzes/{id}', [QuestionController::class, 'updateQuiz']);
Route::delete('/public/admin/quizzes/{id}', [QuestionController::class, 'deleteQuiz']);
Route::post('/public/admin/quizzes/{id}/detach-questions', [QuestionController::class, 'detachQuizQuestions']);
Route::put('/public/admin/quizzes/{id}/status', [QuestionController::class, 'updateQuizStatus']);

Route::post('/public/admin/boards', [SubjectManagementController::class, 'storeBoard']);
Route::put('/public/admin/boards/{board}', [SubjectManagementController::class, 'updateBoard']);
Route::delete('/public/admin/boards/{board}', [SubjectManagementController::class, 'destroyBoard']);
Route::post('/public/admin/boards/bulk-delete', [SubjectManagementController::class, 'bulkDeleteBoards']);

Route::post('/public/admin/standards', [SubjectManagementController::class, 'storeStandard']);
Route::put('/public/admin/standards/{standard}', [SubjectManagementController::class, 'updateStandard']);
Route::delete('/public/admin/standards/{standard}', [SubjectManagementController::class, 'destroyStandard']);
Route::post('/public/admin/standards/bulk-delete', [SubjectManagementController::class, 'bulkDeleteStandards']);

Route::post('/public/admin/subjects', [SubjectManagementController::class, 'store']);
Route::put('/public/admin/subjects/{subject}', [SubjectManagementController::class, 'update']);
Route::delete('/public/admin/subjects/{subject}', [SubjectManagementController::class, 'destroy']);
Route::post('/public/admin/subjects/bulk-delete', [SubjectManagementController::class, 'bulkDeleteSubjects']);

Route::post('/public/admin/chapters', [ChapterController::class, 'store']);
Route::put('/public/admin/chapters/{chapter}', [ChapterController::class, 'update']);
Route::delete('/public/admin/chapters/{chapter}', [ChapterController::class, 'destroy']);
Route::post('/public/admin/chapters/bulk-delete', [ChapterController::class, 'bulkDelete']);

// ── Student auth routes ──
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    Route::get('/quiz-attempts', [QuizAttemptController::class, 'index']);
    Route::post('/quiz-attempts', [QuizAttemptController::class, 'store']);
    Route::get('/quiz-attempts/{id}', [QuizAttemptController::class, 'show']);
    Route::delete('/quiz-attempts/{id}', [QuizAttemptController::class, 'destroy']);
    Route::get('/quiz-rank', [QuizAttemptController::class, 'rank']);

    Route::get('/my-rank', [RankController::class, 'myRank']);

    Route::get('/rank', [RankController::class, 'top']);

    Route::get('/weekly-leaderboard', [\App\Http\Controllers\Api\WeeklyLeaderboardController::class, 'index']);
});

Route::post('/contact', [ContactController::class, 'store']);

// ── Admin auth routes ──
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/verify', [AdminAuthController::class, 'verify']);
    Route::get('/admin/stats', [AdminDashboardController::class, 'index']);
    Route::get('/admin/results', [AdminResultController::class, 'index']);

    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/messages', [ContactController::class, 'index']);
    Route::put('/messages/{id}/read', [ContactController::class, 'markAsRead']);
    Route::put('/messages/read-all', [ContactController::class, 'markAllRead']);
    Route::delete('/messages/{id}', [ContactController::class, 'destroy']);
    Route::delete('/messages', [ContactController::class, 'destroyAll']);

    Route::get('/questions', [QuestionController::class, 'index']);
    Route::get('/questions/options', [QuestionController::class, 'getOptions']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::put('/questions/{id}', [QuestionController::class, 'update']);
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy']);
    Route::post('/questions/bulk-delete', [QuestionController::class, 'bulkDelete']);
    Route::post('/questions/bulk-store', [QuestionController::class, 'bulkStore']);

    Route::post('/quizzes', [QuestionController::class, 'storeQuiz']);
    Route::get('/quizzes', [QuestionController::class, 'getQuizzes']);
    Route::get('/quizzes/filter', [QuestionController::class, 'getQuizzesByFilter']);
    Route::get('/quizzes/{id}', [QuestionController::class, 'getQuiz']);
    Route::put('/quizzes/{id}', [QuestionController::class, 'updateQuiz']);
    Route::delete('/quizzes/{id}', [QuestionController::class, 'deleteQuiz']);
    Route::post('/quizzes/{id}/detach-questions', [QuestionController::class, 'detachQuizQuestions']);
    Route::put('/quizzes/{id}/status', [QuestionController::class, 'updateQuizStatus']);

    Route::get('/quiz-questions', [QuizQuestionController::class, 'index']);
    Route::post('/quiz-questions', [QuizQuestionController::class, 'store']);
    Route::put('/quiz-questions/{id}', [QuizQuestionController::class, 'update']);
    Route::delete('/quiz-questions/{id}', [QuizQuestionController::class, 'destroy']);
    Route::post('/quiz-questions/bulk-delete', [QuizQuestionController::class, 'bulkDelete']);

    Route::get('/boards', [SubjectManagementController::class, 'boards']);
    Route::post('/boards', [SubjectManagementController::class, 'storeBoard']);
    Route::post('/boards/bulk-delete', [SubjectManagementController::class, 'bulkDeleteBoards']);
    Route::put('/boards/{board}', [SubjectManagementController::class, 'updateBoard']);
    Route::delete('/boards/{board}', [SubjectManagementController::class, 'destroyBoard']);

    Route::get('/boards/{board}/standards', [SubjectManagementController::class, 'standards']);
    Route::get('/standards', [SubjectManagementController::class, 'allStandards']);
    Route::post('/standards', [SubjectManagementController::class, 'storeStandard']);
    Route::post('/standards/bulk-delete', [SubjectManagementController::class, 'bulkDeleteStandards']);
    Route::put('/standards/{standard}', [SubjectManagementController::class, 'updateStandard']);
    Route::delete('/standards/{standard}', [SubjectManagementController::class, 'destroyStandard']);

    Route::get('/standards/{standard}/subjects', [SubjectManagementController::class, 'subjectsForStandard']);
    Route::get('/subjects/manage', [SubjectManagementController::class, 'index']);
    Route::post('/subjects/manage', [SubjectManagementController::class, 'store']);
    Route::post('/subjects/manage/bulk-delete', [SubjectManagementController::class, 'bulkDeleteSubjects']);
    Route::put('/subjects/manage/{subject}', [SubjectManagementController::class, 'update']);
    Route::delete('/subjects/manage/{subject}', [SubjectManagementController::class, 'destroy']);

    Route::get('/subjects', [SubjectManagementController::class, 'nested']);

    Route::get('/chapters', [ChapterController::class, 'index']);
    Route::get('/subjects/{subject}/chapters', [ChapterController::class, 'bySubject']);
    Route::post('/chapters', [ChapterController::class, 'store']);
    Route::post('/chapters/bulk-delete', [ChapterController::class, 'bulkDelete']);
    Route::put('/chapters/{chapter}', [ChapterController::class, 'update']);
    Route::delete('/chapters/{chapter}', [ChapterController::class, 'destroy']);

    Route::post('/admin/change-password', [AdminAuthController::class, 'changePassword']);
});
