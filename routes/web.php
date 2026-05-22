<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('profile.index');
})->middleware('auth')->name('dashboard');

// Tours
Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/{tour:slug}', [TourController::class, 'show'])->name('tours.show');

// AI (public) — throttle bảo vệ Gemini API quota
Route::post('/ai/chat', [AiController::class, 'chat'])
    ->middleware('throttle:30,1')
    ->name('ai.chat');
Route::match(['get','post'], '/ai/recommend', [AiController::class, 'recommend'])
    ->middleware('throttle:10,1')
    ->name('ai.recommend');
Route::post('/ai/track', [AiController::class, 'track'])->name('ai.track');

// Auth required
Route::middleware('auth')->group(function () {
    // Booking
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::patch('/bookings/{booking}/cancel', [ProfileController::class, 'cancelBooking'])->name('bookings.cancel');

    // Payment
    Route::get('/payment/{booking}', [PaymentController::class, 'select'])->name('payment.select');
    Route::get('/payment/{booking}/vnpay', [PaymentController::class, 'payVNPay'])->name('payment.vnpay');
    Route::get('/payment/{booking}/momo', [PaymentController::class, 'payMoMo'])->name('payment.momo');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Wishlist
    Route::post('/wishlist/{tour}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/edit', fn() => redirect()->route('profile.index'))->name('profile.edit');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Payment callbacks
Route::get('/payment/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
Route::get('/payment/vnpay/ipn', [PaymentController::class, 'vnpayIpn'])->name('payment.vnpay.ipn'); 

Route::get('/payment/momo/return', [PaymentController::class, 'momoReturn'])->name('payment.momo.return');
Route::post('/payment/momo/notify', [PaymentController::class, 'momoNotify'])->name('payment.momo.notify')
     ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

require __DIR__.'/auth.php';