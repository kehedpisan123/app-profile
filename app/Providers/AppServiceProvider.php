<?php

namespace App\Providers;

use App\Enums\MenuItemType;
use BezhanSalleh\LanguageSwitch\Enums\Placement;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Translatable;

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
        $this->app->make(Translatable::class)
            ->fallback(fallbackLocale: 'id', fallbackAny: true)
            ->locales(['id', 'en']);

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

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch): void {
            $switch
                ->locales(['id', 'en'])
                ->labels([
                    'id' => 'Bahasa Indonesia',
                    'en' => 'English',
                ])
                ->visible(outsidePanels: true)
                ->outsidePanelPlacement(Placement::TopEnd);
        });
    }
}
