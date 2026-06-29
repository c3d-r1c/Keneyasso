<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Docteurs\Http\Controllers\DocteursController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('docteurs', DocteursController::class)->names('docteurs');
});
