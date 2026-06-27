<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Patients\Presentation\Http\Controllers\PatientController;

Route::prefix('patients')->name('patients.')->group(function (): void {
    Route::post('/', [PatientController::class, 'store'])->name('store');
});
