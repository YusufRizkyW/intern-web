<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        // Register Livewire component
        Livewire::component('admin.notification-bell', \App\Livewire\Admin\NotificationBell::class);
        
        // Register observer untuk auto-notification
        \App\Models\PendaftaranMagang::observe(\App\Observers\PendaftaranMagangObserver::class);
    }
}
