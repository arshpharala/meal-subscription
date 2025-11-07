<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\RoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\AdminController;
use App\Http\Controllers\Admin\Auth\ModuleController;
use App\Http\Controllers\Admin\CMS\SettingController;
use App\Http\Controllers\Admin\CMS\TinyMCEController;
use App\Http\Controllers\Admin\Catalog\MealController;
use App\Http\Controllers\Admin\CMS\AttachmentController;
use App\Http\Controllers\Admin\Sales\CustomerController;
use App\Http\Controllers\Admin\Auth\PermissionController;
use App\Http\Controllers\Admin\Catalog\PackageController;
use App\Http\Controllers\Admin\Catalog\CaloriesController;
use App\Http\Controllers\Admin\Sales\PaymentLinkController;
use App\Http\Controllers\Admin\Sales\SubscriptionController;
use App\Http\Controllers\Admin\Catalog\MealCalorieController;
use App\Http\Controllers\Admin\Catalog\MealPackageController;
use App\Http\Controllers\Admin\Sales\CustomerAddressController;
use App\Http\Controllers\Admin\Catalog\MealExtraChargeController;
use App\Http\Controllers\Admin\Catalog\MealPackagePriceController;
use App\Http\Controllers\Admin\Sales\SubscriptionFreezeController;
use App\Http\Controllers\Admin\Sales\CustomerSubscriptionController;
use App\Http\Controllers\Admin\Catalog\MealPackageDurationController;

Route::prefix('dashboard')->controller(DashboardController::class)->group(function () {
    Route::get('/',                     'dashboard')->name('dashboard');
});


Route::group(['prefix' => '/auth', 'as' => 'auth.'], function () {

    Route::resource('modules',                             ModuleController::class);
    Route::resource('permissions',                         PermissionController::class);
    Route::resource('roles',                               RoleController::class);
    Route::resource('admins',                              AdminController::class);
});

Route::group(['prefix' => '/catalog', 'as' => 'catalog.'], function () {
    Route::resource('meals',                                MealController::class);
    Route::resource('packages',                             PackageController::class);
    Route::resource('calories',                             CaloriesController::class);
    Route::resource('meal.packages',                        MealPackageController::class);
    Route::resource('meal.package.prices',                  MealPackagePriceController::class);
});

Route::group(['prefix' => '/sales', 'as' => 'sales.'], function () {

    Route::resource('customers',                            CustomerController::class);
    Route::resource('customer.addresses',                   CustomerAddressController::class);
    Route::resource('customer.subscriptions',               CustomerSubscriptionController::class);
    Route::resource('subscriptions',                        SubscriptionController::class);
    Route::resource('payment-links',                        PaymentLinkController::class);

    Route::resource('subscription.freezes', SubscriptionFreezeController::class)->only(['create', 'store', 'destroy']);

    Route::post('/subscriptions/{subscription}/renewals/{renewalId}/retry',        [SubscriptionController::class, 'retryRenewal'])->name('subscription.renewals.retry');
    Route::post('/subscriptions/{subscription}/manual-renew',        [SubscriptionController::class, 'manualRenew'])->name('subscription.manualRenew');
});



Route::group(['prefix' => '/cms', 'as' => 'cms.'], function () {

    Route::resource('attachments',                          AttachmentController::class);
    Route::resource('settings',                             SettingController::class);


    Route::post('upload/tinymce',                            [TinyMCEController::class, 'upload'])->name('upload.tinymce');
});

Route::group(['prefix' => '/ajax', 'as' => 'ajax.'], function () {


    Route::get('meal/{meal}/packages', [CustomerSubscriptionController::class,  'getPackages'])->name('meals.packages');
    Route::get('meal/{meal}/package/{package}/prices', [CustomerSubscriptionController::class,  'getPrices'])->name('meals.package.prices');
    Route::get('meal/{meal}/package/{package}/prices', [CustomerSubscriptionController::class,  'getPrices'])->name('meals.package.prices');
    Route::get('customer/{customer}/meal-package-price/{price}/summary', [CustomerSubscriptionController::class,  'getSummary'])->name('meals.package.price.summary');

    Route::get('checkout/packages/{meal}', [PaymentLinkController::class, 'getPackages'])->name('packages');
    Route::get('checkout/prices/{mealPackage}', [PaymentLinkController::class, 'getPrices'])->name('prices');
    Route::post('checkout/summary', [PaymentLinkController::class, 'getReview'])->name('summary');

    Route::get('/customers/find',         [PaymentLinkController::class, 'findCustomer'])->name('customers.find'); // ?email= OR ?q=
    Route::get('/customers/{user}/addresses', [PaymentLinkController::class, 'getCustomerAddresses'])->name('customers.addresses');
    Route::post('/customers/{user}/addresses', [PaymentLinkController::class, 'storeCustomerAddress'])->name('customers.addresses.store');
});
