<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\FabricController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\TaskController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes (if not using web auth for API)
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Authentication logout
    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy']);

    // V1 API routes for mobile app compatibility
    Route::prefix('V1')->group(function () {
        // Attendance API endpoints for mobile app
        Route::prefix('attendance')->group(function () {
            Route::post('/checkInOut', [AttendanceController::class, 'apiCheckInOut']);
            Route::get('/checkStatus', [AttendanceController::class, 'apiCheckStatus']);
            Route::get('/getHistory', [AttendanceController::class, 'apiGetHistory']);
            Route::post('/statusUpdate', [AttendanceController::class, 'apiStatusUpdate']);
            Route::post('/startStopBreak', [AttendanceController::class, 'apiStartStopBreak']);
            Route::post('/validateGeoLocation', [AttendanceController::class, 'apiValidateGeoLocation']);
            Route::post('/setEarlyCheckoutReason', [AttendanceController::class, 'apiSetEarlyCheckoutReason']);
        });
        Route::prefix('task')->group(function () {
            Route::get('/GetAll', [TaskController::class, 'apiGetAllTasks']);
            Route::post('/startTask', [TaskController::class, 'apiStartTask']);
            Route::post('/completeTask', [TaskController::class, 'apiCompleteTask']);
            Route::post('/holdTask', [TaskController::class, 'apiHoldTask']);
            Route::post('/resumeTask', [TaskController::class, 'apiResumeTask']);
            Route::get('/getTaskUpdates', [TaskController::class, 'apiGetTaskUpdates']);
        });

        // Authentication & User Management
        Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'apiLogin']);
        Route::get('/account/me', [App\Http\Controllers\UserController::class, 'me']);
        Route::post('/user/updateStatus', [App\Http\Controllers\UserController::class, 'updateStatus']);

        // Additional Required APIs
        Route::get('/notification/getAll', [App\Http\Controllers\NotificationController::class, 'getAll']);
        Route::get('/settings/getAppSettings', [App\Http\Controllers\SettingsController::class, 'getAppSettings']);
        Route::get('/getDashboardData', [App\Http\Controllers\DashboardController::class, 'getDashboardData']);
    });

    // Orders API
    Route::apiResource('orders', OrderController::class);

    // Staff API
    Route::apiResource('staff', StaffController::class);

    // Fabrics API
    Route::apiResource('fabrics', FabricController::class);

    // Masters API (Garments)
    Route::apiResource('masters', MasterController::class);

    // Measurements API (part of masters)
    Route::prefix('masters')->group(function () {
        Route::get('/measurements', [MasterController::class, 'measurements']);
        Route::post('/measurements', [MasterController::class, 'createMeasurements']);
        Route::put('/measurements/{id}', [MasterController::class, 'updateMeasurements']);
        Route::delete('/measurements/{id}', [MasterController::class, 'destroyMeasurements']);
        Route::post('/import-measurements', [MasterController::class, 'importMeasurements']);
        Route::post('/import-garments', [MasterController::class, 'importGarments']);
    });

    // Attendance API
    Route::apiResource('attendance', AttendanceController::class);

    // Dashboard API
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Relations API (Garment-Measurement relations)
    Route::apiResource('relations', RelationController::class);
    Route::get('/relations/measurements/{id}', [RelationController::class, 'getMeasurements']);

    // Roles API
    Route::apiResource('roles', RoleController::class);
    Route::post('/roles/import', [RoleController::class, 'import']);

    // Salary API
    Route::apiResource('salary', SalaryController::class);
    
    // Additional specific routes for better API structure
    Route::prefix('staff')->group(function () {
        // Staff salary related routes
        Route::get('/{id}/salary', [SalaryController::class, 'show']);
        Route::post('/{id}/salary', [SalaryController::class, 'store']);
    });

    // Additional fabric specific routes
    Route::prefix('fabrics')->group(function () {
        Route::post('/import', [FabricController::class, 'importFabrics']);
    });

    // Additional order specific routes (if needed)
    Route::prefix('orders')->group(function () {
        // Add any order-specific API endpoints here
        // For example: order statistics, order by status, etc.
    });
});
