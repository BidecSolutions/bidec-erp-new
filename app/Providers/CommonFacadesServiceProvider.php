<?php

namespace App\Providers;
use App;
use Illuminate\Support\ServiceProvider;

class CommonFacadesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        App::bind('common',function() {
            return new \App\Facades\CommonFacades;
         });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
