<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('anggotas', AnggotaController::class);

    // Verifikasi & penolakan: hanya admin, pengurus, bendahara.
    // Didaftarkan SEBELUM resource agar tidak bentrok dengan {simpanan}.
    Route::middleware(RoleMiddleware::class . ':admin,pengurus,bendahara')->group(function () {
        Route::post('/simpanans/{simpanan}/verifikasi', [SimpananController::class, 'verifikasi'])
            ->name('simpanans.verifikasi');
        Route::post('/simpanans/{simpanan}/tolak', [SimpananController::class, 'tolak'])
            ->name('simpanans.tolak');
    });

    Route::resource('simpanans', SimpananController::class);

    Route::middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'update']);
    });
});

require __DIR__ . '/auth.php';
