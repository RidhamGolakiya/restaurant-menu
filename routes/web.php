<?php

use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RestaurantTimingController;
use App\Http\Controllers\UnsubscribeUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
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
