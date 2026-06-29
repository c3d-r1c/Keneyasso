<?php

declare(strict_types=1);

use App\Services\SidebarRegistry;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $items = app(SidebarRegistry::class)->items();

    return view('home', ['first' => $items[0] ?? null]);
})->name('home');
