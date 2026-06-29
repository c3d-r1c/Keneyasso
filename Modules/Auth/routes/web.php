<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

Route::name('auth.')->group(function (): void {
    Route::prefix('admin/roles')->name('roles.')->group(function (): void {
        Route::get('/', function () {
            return view('auth::roles.index', [
                'totalRoles' => Role::count(),
                'totalPermissions' => Permission::count(),
            ]);
        })->name('index');
    });
});
