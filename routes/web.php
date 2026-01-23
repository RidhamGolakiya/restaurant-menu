<?php

use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RestaurantTimingController;
use App\Http\Controllers\UnsubscribeUserController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\DemoBookingController;
use Illuminate\Support\Facades\Route;

Route::post('/demo-booking', [DemoBookingController::class, 'store'])->name('demo.store');

Route::get('/qr/{uuid}', [QrCodeController::class, 'scan'])->name('qr.scan');

Route::get('/', function () {
    $restaurants = \App\Models\Restaurant::where('show_on_landing_page', true)->get();
    return view('landing-page', compact('restaurants'));
});
Route::post('/restaurant/timing/save', [RestaurantTimingController::class, 'save'])
    ->name('restaurant.timing.save')
    ->middleware(['auth']);

Route::get('/unsubscribe', [UnsubscribeUserController::class, 'index']);
Route::post('/unsubscribe', [UnsubscribeUserController::class, 'unsubscribe']);

Route::get('r/{slug}', [RestaurantController::class, 'index'])->name('restaurant.index')->where('slug', '[a-zA-Z0-9\-]+');
Route::post('r/{slug}/reservation', [RestaurantController::class, 'reservation'])->name('reservation.store')->where('slug', '[a-zA-Z0-9\-]+');
Route::get('r/{slug}/slots', [RestaurantController::class, 'getTimeSlots'])->name('restaurant.slots')->where('slug', '[a-zA-Z0-9\-]+');
Route::get('r/{slug}/check-availability', [RestaurantController::class, 'checkAvailability']);
Route::get('r/{slug}/c/{categorySlug}', [RestaurantController::class, 'category'])->name('restaurant.category');
Route::get('r/{slug}/p/{productSlug}', [RestaurantController::class, 'product'])->name('restaurant.product');
Route::get('r/{slug}/best-seller-food', [RestaurantController::class, 'bestSellerFood'])->name('restaurant.best-seller.food');
Route::get('r/{slug}/best-seller-drink', [RestaurantController::class, 'bestSellerDrink'])->name('restaurant.best-seller.drink');

// Catch-all route for React SPA (Theme 4)
// This ensures that reloading pages like /best-sellers works correctly by serving the index view
Route::get('r/{slug}/{any}', [RestaurantController::class, 'index'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->where('any', '.*');