<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        // Share authenticated user's preferences across all Inertia responses
        Inertia::share('auth.user.preferences', function () {
            $user = auth()->user();

            return $user ? ($user->preferences ?? null) : null;
        });
    }
}
