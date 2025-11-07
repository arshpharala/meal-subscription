<?php

namespace App\Providers;

use App\Observers\MealPriceObserver;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AdminAuthenticate;
use App\Models\Catalog\MealPackageDuration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        // LogViewer::auth(function ($request) {
        //     return Auth::guard('admin')->check();
        // });

    }

    protected function registerRoutes()
    {

        Route::middleware(['web'])
            ->prefix('admin')
            ->as('admin.')
            ->group(base_path('routes/admin-auth.php'));

        Route::middleware(['web', AdminAuthenticate::class])
            ->prefix('admin')
            ->as('admin.')
            ->group(base_path('routes/admin.php'));

        Route::prefix('api')
            ->as('api.')
            ->group(base_path('routes/api.php'));
    }
}
