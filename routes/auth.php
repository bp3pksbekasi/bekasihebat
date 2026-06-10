<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    if (! Route::has('login')) {
        Volt::route('login', 'auth.login')
            ->name('login');
    }

    if (! Route::has('register')) {
        if (class_exists(\App\Livewire\KartuAnggota\Register::class)) {
            Route::get('/register', \App\Livewire\KartuAnggota\Register::class)->name('register');
        } else {
            Volt::route('register', 'auth.register')
                ->name('register');
        }
    }

    Volt::route('forgot-password', 'auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'auth.reset-password')
        ->name('password.reset');

});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'auth.confirm-password')
        ->name('password.confirm');
});

if (! Route::has('login') && class_exists(\App\Livewire\Public\Auth\Login::class)) {
    Route::get('/login', \App\Livewire\Public\Auth\Login::class)->name('login');
}

if (! Route::has('profile.complete') && class_exists(\App\Livewire\Public\Profile\Complete::class)) {
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile/complete', \App\Livewire\Public\Profile\Complete::class)->name('profile.complete');
    });
}

if (! Route::has('member.dashboard') && class_exists(\App\Livewire\Public\Dashboard::class)) {
    Route::middleware(['auth'])->group(function () {
        Route::middleware('profile.completed')->group(function () {
            Route::get('/member/dashboard', \App\Livewire\Public\Dashboard::class)->name('member.dashboard');
        });
    });
}
