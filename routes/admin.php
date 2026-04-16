<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReviewController;

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
          Route::patch('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
          Route::patch('bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');

        // Users
        Route::resource('users', UserController::class)
             ->only(['index', 'show', 'edit', 'update', 'destroy']);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
             ->name('users.toggle-status');

          // Reviews
          Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
          Route::patch('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
          Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });