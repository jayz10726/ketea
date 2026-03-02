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

    // Consumables — VISIBLE TO ALL (view only for staff)
    Route::get('consumables', [ConsumableController::class, 'index'])->name('consumables.index');
    Route::post('consumables', [ConsumableController::class, 'store'])
         ->name('consumables.store')
         ->middleware('role:admin,storekeeper');
    Route::put('consumables/{consumable}', [ConsumableController::class, 'update'])
         ->name('consumables.update')
         ->middleware('role:admin,storekeeper');
    Route::delete('consumables/{consumable}', [ConsumableController::class, 'destroy'])
         ->name('consumables.destroy')
         ->middleware('role:admin,storekeeper');

    // Assets — admin & storekeeper only
    Route::resource('assets', AssetController::class)
         ->middleware('role:admin,storekeeper');

    // Issuances — admin & storekeeper
    Route::resource('issuances', IssuanceController::class)
         ->middleware('role:admin,storekeeper');

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