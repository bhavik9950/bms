<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\RelationController;
use App\Http\Controllers\FabricController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SalaryController;
Route::get('/', function () {
    return redirect()->route('login');
});

// Main Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Orders under dashboard
Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {
    Route::resource('orders', OrderController::class)->names([
        'index' => 'dashboard.orders',
        'create' => 'dashboard.orders.create',
        'store' => 'dashboard.orders.store',
        'show' => 'dashboard.orders.show',
        'edit' => 'dashboard.orders.edit',
        'update' => 'dashboard.orders.update',
        'destroy' => 'dashboard.orders.destroy',
    ]);
});


// Master routes
Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {
    Route::resource('masters', MasterController::class)
        ->except(['show']) 
        ->names([
            'index'   => 'dashboard.masters',
            'create'  => 'dashboard.masters.create',
            'store'   => 'dashboard.masters.store',
            'edit'    => 'dashboard.masters.edit',
            'update'  => 'dashboard.masters.update',
            'destroy' => 'dashboard.masters.destroy',
        ]);

    // custom route for measurements
    Route::get('/masters/measurements', [MasterController::class, 'measurements'])
        ->name('dashboard.masters.measurements');

    
    Route::post('/masters/import-garments', [MasterController::class, 'importGarments'])
    ->name('dashboard.masters.importGarments');

Route::post('/masters/import-measurements', [MasterController::class, 'importMeasurements'])
    ->name('dashboard.masters.importMeasurements');
Route::delete('/masters/measurement/{id}', [MasterController::class, 'destroyMeasurements'])
    ->name('dashboard.masters.destroyMeasurements');
        Route::post('/masters/create-measurement/', [MasterController::class, 'createMeasurements'])
        ->name('dashboard.masters.createMeasurements');
    Route::put('/masters/update-measurement/{id}', [MasterController::class, 'updateMeasurements'])
        ->name('dashboard.masters.updateMeasurements');
});

//  Garment and Measurement relation routes
Route::prefix('dashboard/masters')->group(function () {
    Route::get('/relations', [RelationController::class, 'index'])->name('dashboard.masters.relations');
    Route::post('/relations', [RelationController::class, 'store'])->name('dashboard.masters.relations.store');
    Route::get('relations/view', [RelationController::class, 'view'])->name('dashboard.masters.relations.view');   
    Route::put('/relations/{id}', [RelationController::class, 'update'])->name('dashboard.masters.relations.update');
    Route::delete('/relations/{id}', [RelationController::class, 'destroy'])->name('dashboard.masters.relations.destroy');
    Route::get('/relations/{id}/measurements',[RelationController::class,'getMeasurements'])->name('dashboard.masters.getMeasurements');
});

// Fabric routes
Route::prefix('dashboard/masters')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/fabrics', [FabricController::class, 'index'])->name('dashboard.masters.fabrics');
    Route::post('/import-fabrics', [FabricController::class, 'importFabrics'])->name('dashboard.masters.importfabrics');
    Route::post('/create-fabric', [FabricController::class, 'createFabric'])->name('dashboard.masters.createFabric');
    Route::post('/store-fabric', [FabricController::class, 'store'])->name('dashboard.masters.storeFabric');
    Route::put('/update-fabric/{id}', [FabricController::class, 'updateFabric'])->name('dashboard.masters.updateFabric');
    Route::delete('/delete-fabric/{id}', [FabricController::class, 'destroyFabric'])->name('dashboard.masters.destroyFabric');
});

// Staff routes
Route::prefix('dashboard/staff')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [StaffController::class, 'index'])->name('dashboard.staff');
    Route::get('/create', [StaffController::class, 'create'])->name('dashboard.staff.create');
      Route::get('/edit/{id}', [StaffController::class, 'edit'])->name('dashboard.staff.edit');
    Route::put('/edit/{id}', [StaffController::class, 'update'])->name('dashboard.staff.update');
    Route::post('/store', [StaffController::class, 'store'])->name('dashboard.staff.store');
    Route::delete('/delete/{id}', [StaffController::class, 'destroy'])->name('dashboard.staff.destroy');
   Route::get('/salary', [SalaryController::class, 'index'])->name('dashboard.staff.salary');
   Route::get('/salary/{id}', [SalaryController::class, 'show'])->name('dashboard.staff.salary.view');
});

// Roles management routes
Route::prefix('dashboard/roles')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('dashboard.roles');
    Route::post('/import-roles', [RoleController::class, 'import'])->name('dashboard.roles.import');
    Route::post('/create', [RoleController::class, 'store'])->name('dashboard.roles.store');
    Route::put('/update/{id}', [RoleController::class, 'update'])->name('dashboard.roles.update');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->name('dashboard.roles.destroy');
});

// Attendence routes
Route::prefix('dashboard/attendance')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('dashboard.attendance');
    
});

require __DIR__.'/auth.php';

