<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AcademicController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\CourseOfferingController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\GradeController;
use App\Http\Controllers\Api\V1\AcademicRecordController;

Route::get('/docs', fn () => response()->file(base_path('docs/openapi.yaml'), ['Content-Type' => 'application/yaml']));

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::get('/students', [StudentController::class, 'index']);
        Route::post('/students', [StudentController::class, 'store']);
        Route::get('/students/{student}', [StudentController::class, 'show']);
        Route::match(['put', 'patch'], '/students/{student}', [StudentController::class, 'update']);
        Route::delete('/students/{student}', [StudentController::class, 'destroy']);
        foreach (['programs', 'courses', 'academic-terms'] as $resource) {
            Route::get('/'.$resource, [AcademicController::class, 'index'])->defaults('resource', $resource);
            Route::post('/'.$resource, [AcademicController::class, 'store'])->defaults('resource', $resource);
            Route::get('/'.$resource.'/{id}', [AcademicController::class, 'show'])->defaults('resource', $resource);
            Route::match(['put', 'patch'], '/'.$resource.'/{id}', [AcademicController::class, 'update'])->defaults('resource', $resource);
            Route::delete('/'.$resource.'/{id}', [AcademicController::class, 'destroy'])->defaults('resource', $resource);
        }
        Route::apiResource('course-offerings', CourseOfferingController::class)->parameters(['course-offerings' => 'courseOffering']);
        Route::get('/course-offerings/{courseOffering}/students', [CourseOfferingController::class, 'students']);
        Route::apiResource('enrollments', EnrollmentController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::get('/students/{student}/enrollments', [EnrollmentController::class, 'studentEnrollments']);
        Route::apiResource('grades', GradeController::class)->only(['index', 'store', 'show', 'update']);
        Route::get('/students/{student}/grades', [GradeController::class, 'studentGrades']);
        Route::get('/students/{student}/academic-record', [AcademicRecordController::class, 'show']);
    });
});
