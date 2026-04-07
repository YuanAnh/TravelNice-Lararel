<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\BookingController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::resource('users', UserController::class);

        // Tours
        Route::resource('tours', TourController::class);

        // Bookings
        Route::resource('bookings', BookingController::class);
    });