<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // View Composer for nav-header to load user notes
        view()->composer('partials.nav-header', function ($view) {
            if (auth()->check()) {
                $notes = \App\Models\UserNote::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->get();
                $view->with('userNotes', $notes);
            }
        });
    }
}
