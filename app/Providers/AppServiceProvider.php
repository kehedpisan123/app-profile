<?php

namespace App\Providers;

use App\Enums\MenuItemType;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        Relation::morphMap([
            MenuItemType::Page->value => MenuItemType::Page->linkableModel(),
            MenuItemType::Post->value => MenuItemType::Post->linkableModel(),
            MenuItemType::Category->value => MenuItemType::Category->linkableModel(),
        ]);

        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('questions', function (Request $request): array {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perDay(20)->by($request->ip()),
            ];
        });
    }
}
