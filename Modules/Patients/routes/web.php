<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Patients\Http\Controllers\PatientController;

Route::name('doclinic.')->group(function (): void {
    Route::get('/patients', fn () => view('patients::patients'))->name('patients');
    Route::get('/patients/{id}', fn (string $id) => view('patients::patient_details', compact('id')))->name('patient_details');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
});
