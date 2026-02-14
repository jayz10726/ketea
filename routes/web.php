<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ConsumableController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    // Dashboard — all roles
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Assets — admin & storekeeper
    Route::resource('assets', AssetController::class)
         ->middleware('role:admin,storekeeper');

    // Consumables — admin & storekeeper
    Route::resource('consumables', ConsumableController::class)
         ->middleware('role:admin,storekeeper');

    // Issuances — admin & storekeeper
    Route::resource('issuances', IssuanceController::class)
         ->middleware('role:admin,storekeeper');

    // Return an item
    Route::post('issuances/{issuance}/return', [IssuanceController::class, 'returnItem'])
         ->name('issuances.return')
         ->middleware('role:admin,storekeeper');

    // Reports — admin only
    Route::get('reports', [ReportController::class, 'index'])
         ->name('reports.index')
         ->middleware('role:admin');

    Route::get('reports/export', [ReportController::class, 'exportCsv'])
         ->name('reports.export')
         ->middleware('role:admin');

    // User management — admin only
    Route::resource('users', UserController::class)
         ->middleware('role:admin');
});