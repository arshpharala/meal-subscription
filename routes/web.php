<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\SubscriptionController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

Route::get('/checkout/{checkout}', [CheckoutController::class, 'show'])
    ->middleware('signed')
    ->name('checkout.portal.show');

Route::get('/payment/{id}/success', [CheckoutController::class, 'success'])->name('payment.success');
Route::get('/payment/{id}/cancel', [CheckoutController::class, 'cancel'])->name('payment.cancel');

// Route::group(['prefix' => 'meals', 'as' => 'meals.'], function () {
//     Route::get('/', [PageController::class, 'meals'])->name('index');
//     Route::get('/{slug}', [PageController::class, 'mealDetails'])->name('show');
// });

Route::prefix('ajax/')->name('ajax.')->group(function () {

    Route::get('cities/{province}',                     [Controller::class, 'getCities'])->name('province.cities');
    Route::get('areas/{city}',                          [Controller::class, 'getAreas'])->name('city.areas');
});


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth', 'verified');

Route::middleware(['auth', 'verified'])->name('customer.')->group(function () {

    Route::resource('/subscriptions', SubscriptionController::class)->only(['index', 'show']);
    Route::resource('/addresses', AddressController::class);
    Route::resource('/payments', AddressController::class);

    Route::get('/profile', [AddressController::class, 'index'])->name('profile');
});


require __DIR__ . '/auth.php';
