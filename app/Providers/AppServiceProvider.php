<?php

namespace App\Providers;

use App\Helpers\DashboardHelper;
use Illuminate\Support\ServiceProvider;
use App\Helpers\TokenHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\LanguageHelper;
use App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('token', function ($app) {
            $token = new TokenHelper();

            return $token;
        });

        $this->app->singleton('response', function ($app) {
            $response = new ResponseHelper();

            return $response;
        });

        $this->app->singleton('language', function ($app) {
            $lang = new LanguageHelper();

            return $lang;
        });

        $this->app->singleton('dashboard', function ($app) {
            $dashboard = new DashboardHelper();

            return $dashboard;
        });

	    $this->app->singleton('calendar', function ($app) {
		    $calendar = new App\Helpers\CalendarHelper();

		    return $calendar;
	    });
    }
}
