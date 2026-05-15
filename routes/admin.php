<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\ReportController;

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

           // Banners
        Route::get('banners', [BannerController::class, 'index'])->name('banners.index');
        Route::post('banners', [BannerController::class, 'store'])->name('banners.store');
        Route::put('banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
        Route::delete('banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
        Route::patch('banners/{banner}/toggle', [BannerController::class, 'toggleActive'])->name('banners.toggle');
 
        // Logs
        Route::get('logs', [LogController::class, 'index'])->name('logs.index');
 
        // Reports
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export-bookings', [ReportController::class, 'exportBookingsExcel'])->name('reports.export-bookings');
        Route::get('reports/export-revenue', [ReportController::class, 'exportRevenueExcel'])->name('reports.export-revenue');
        Route::get('reports/pdf-bookings', [ReportController::class, 'exportBookingsPdf'])->name('reports.pdf-bookings');
    });