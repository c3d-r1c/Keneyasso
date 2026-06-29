<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::name('auth.')->group(function (): void {
    Route::prefix('admin/roles')->name('roles.')->group(function (): void {
        Route::get('/', fn () => view('auth::roles.index'))->name('index');
    });
});
