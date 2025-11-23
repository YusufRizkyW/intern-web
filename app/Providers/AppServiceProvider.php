<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Carbon\Carbon;

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

        // Set timezone untuk PHP
        date_default_timezone_set('Asia/Jakarta'); // WIB

         // Set locale untuk Carbon
        Carbon::setLocale('id');
        
    }
}
