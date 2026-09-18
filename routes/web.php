<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/devices')->name('home');

Route::inertia('/privacy-policy', 'PrivacyPolicy')->name('privacy-policy');
Route::inertia('/terms-of-service', 'TermsOfService')->name('terms-of-service');
Route::redirect('/privacy', '/privacy-policy');
Route::redirect('/terms', '/terms-of-service');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('/auth/google/redirect', [AuthenticatedSessionController::class, 'redirectToGoogle'])
        ->middleware('throttle:login')
        ->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthenticatedSessionController::class, 'handleGoogleCallback'])
        ->middleware('throttle:login')
        ->name('auth.google.callback');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
});
