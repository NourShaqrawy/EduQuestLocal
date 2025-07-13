<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExerciseController;
use Illuminate\Support\Facades\Route;


// Routes العامة (بدون مصادقة)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes تتطلب مصادقة
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
       Route::get('/courses', [CourseController::class, 'index']);
    
    // Routes المشتركة لجميع المستخدمين المصادقين
    Route::apiResource('courses', CourseController::class)->only(['index', 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // Routes الخاصة بالمدير
   // بدلاً من except
// Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);

// أو تعريف يدوي بدقة أعلى
Route::middleware('role:admin')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
   Route::post('/courses', [CourseController::class, 'store']);
   Route::put('/courses/{id}', [CourseController::class, 'update']);
   Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
});
    // Routes الخاصة بالناشر
    Route::middleware('role:publisher')->group(function () {
        Route::apiResource('courses', CourseController::class)->except(['index', 'show']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']); // يسمح بالتحديث فقط
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{id}', [CourseController::class, 'update']);
    });

    // Routes الخاصة بالطالب
    Route::middleware('role:student')->group(function () {
        Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll']);
        Route::post('/exercises/{exercise}/submit', [ExerciseController::class, 'submit']);
    });
});
