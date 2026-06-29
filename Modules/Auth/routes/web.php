<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\UserController;

// ─── Routes admin (authentification requise) ──────────────────────────────────

Route::middleware('auth')->name('auth.')->group(function (): void {

    Route::middleware('can:gérer rôles')->prefix('admin/roles')->name('roles.')->group(function (): void {
        Route::get('/', fn () => view('auth::roles.index'))->name('index');
    });

    Route::middleware('can:gérer utilisateurs')->prefix('admin/users')->name('users.')->group(function (): void {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/creer', function () {
            return view('auth::users.create', [
                'roles' => \Spatie\Permission\Models\Role::orderBy('name')->get(),
            ]);
        })->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/modifier', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

});
