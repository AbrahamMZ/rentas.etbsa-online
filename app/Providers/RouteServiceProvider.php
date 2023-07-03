<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('web')
                ->group(
                    function () {
                        # code...
                        include(base_path('routes/web.php'));
                        include(base_path('routes/auth.php'));
                        include(base_path('routes/resources/expenses.php'));
                        include(base_path('routes/resources/users.php'));
                        include(base_path('routes/resources/contacts.php'));
                        include(base_path('routes/resources/organizations.php'));
                        include(base_path('routes/resources/fixesCosts.php'));
                        include(base_path('routes/resources/machineries.php'));
                        include(base_path('routes/resources/leaseIncomes.php'));
                        include(base_path('routes/resources/categories.php'));
                        include(base_path('routes/resources/machineryExpenses.php'));
                        include(base_path('routes/resources/machineryMonthlyExpenses.php'));
                        include(base_path('routes/resources/machineryServiceExpenses.php'));
                        include(base_path('routes/resources/machineryImages.php'));
                    }
                );

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for ('api', function (Request $request) {
            return Limit::perMinute(60);
        });
    }
}