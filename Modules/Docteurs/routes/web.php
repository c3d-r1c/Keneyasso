<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Docteurs\Http\Controllers\DocteurController;

Route::name('doclinic.')->group(function (): void {
    Route::get('/medecins', fn () => view('docteurs::doctor_list'))->name('doctor_list');
    Route::get('/medecins/{id}', fn (string $id) => view('docteurs::doctors', compact('id')))->name('doctors');
    Route::post('/docteurs', [DocteurController::class, 'store'])->name('docteurs.store');
});
