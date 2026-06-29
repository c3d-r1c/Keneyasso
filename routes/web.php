<?php

declare(strict_types=1);

use App\Services\SidebarRegistry;
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\LoginController;

// ─── Authentification (infrastructure core — indépendant du module Auth) ──────
// Ces routes doivent rester actives même si le module Auth est désactivé,
// car le layout app.blade.php et tous les modules y font référence.

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ─── Accueil ──────────────────────────────────────────────────────────────────

Route::get('/', function () {
    $items = app(SidebarRegistry::class)->items();

    return view('home', ['first' => $items[0] ?? null]);
})->middleware('auth')->name('home');
