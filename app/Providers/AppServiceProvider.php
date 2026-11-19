<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        // Seuls les administrateurs peuvent créer ou supprimer des catégories
        Gate::define('gerer-categories', function (User $utilisateur) {
            return $utilisateur->role === 'admin';
        });
    }
}
