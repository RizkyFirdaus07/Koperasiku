<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Anggota: tambah, ubah, hapus hanya untuk admin & pengurus.
    // (didaftarkan lebih dulu agar /anggotas/create tidak tertangkap {anggota})
    Route::middleware('role:admin,pengurus')->group(function () {
        Route::resource('anggotas', AnggotaController::class)
            ->parameters(['anggotas' => 'anggota'])
            ->except(['index', 'show']);
    });

    // Anggota & simpanan: seluruh petugas koperasi.
    Route::middleware('role:admin,pengurus,bendahara')->group(function () {
        Route::resource('anggotas', AnggotaController::class)
            ->parameters(['anggotas' => 'anggota'])
            ->only(['index', 'show']);

        Route::post('/simpanans/{simpanan}/verifikasi', [SimpananController::class, 'verifikasi'])
            ->name('simpanans.verifikasi');

        Route::post('/simpanans/{simpanan}/tolak', [SimpananController::class, 'tolak'])
            ->name('simpanans.tolak');

        Route::resource('simpanans', SimpananController::class)
            ->parameters(['simpanans' => 'simpanan']);
    });

    // Manajemen user: admin saja.
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});

require __DIR__ . '/auth.php';
