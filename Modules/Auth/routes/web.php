<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::name('auth.')->group(function (): void {
    Route::prefix('admin/roles')->name('roles.')->group(function (): void {
        Route::get('/', function () {
            return view('auth::roles.index', [
                'totalRoles' => \Spatie\Permission\Models\Role::count(),
                'totalPermissions' => \Spatie\Permission\Models\Permission::count(),
            ]);
        })->name('index');
    });
});
