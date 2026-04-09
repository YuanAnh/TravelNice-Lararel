<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\UserController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Tours CRUD
        Route::resource('tours', TourController::class);

        // Bookings
        Route::resource('bookings', BookingController::class)
             ->only(['index', 'show', 'edit', 'update', 'destroy']);

        // Users
        Route::resource('users', UserController::class)
             ->only(['index', 'show', 'edit', 'update', 'destroy']);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
             ->name('users.toggle-status');
    });