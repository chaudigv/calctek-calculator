<?php

use App\Http\Controllers\CalculationsController;
use Illuminate\Support\Facades\Route;

Route::prefix('calculations')->group(function () {
    Route::get('/', [CalculationsController::class, 'index'])->name('calculations.index');
    Route::post('/', [CalculationsController::class, 'store'])->name('calculations.store');
    Route::delete('/', [CalculationsController::class, 'destroyAll'])->name('calculations.destroyAll');
    Route::delete('/{calculation}', [CalculationsController::class, 'destroy'])->name('calculations.destroy');
});
