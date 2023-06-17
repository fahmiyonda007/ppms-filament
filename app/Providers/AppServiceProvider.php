<?php

namespace App\Providers;

use App\Filament\Resources\BankAccountResource;
use App\Filament\Resources\BankResource;
use App\Filament\Resources\Shield\RoleResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\VendorResource;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use JeffGreco13\FilamentBreezy\FilamentBreezy;
use Illuminate\Support\Facades\Blade;

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
        FilamentBreezy::setPasswordRules(
            [
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->mixedCase()
                    ->uncompromised(3)
            ]
        );

        Filament::serving(function () {
            // Using Vite
            Filament::registerViteTheme('resources/css/filament.css');
        });

        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };

        Filament::registerNavigationGroups([
            'Masters',
            'Projects',
            'Cash',
            'Reports',
            'Settings',
        ]);

        // Filament::navigation(function (NavigationBuilder $builder): NavigationBuilder {
        //     return $builder
        //         ->items([
        //             NavigationItem::make('Dashboard')
        //                 ->icon('heroicon-o-home')
        //                 ->activeIcon('heroicon-s-home')
        //                 ->isActiveWhen(fn (): bool => request()->routeIs('filament.pages.dashboard'))
        //                 ->url(route('filament.pages.dashboard')),
        //         ])
        //         ->groups([
        //             NavigationGroup::make('settings')
        //                 ->items([
        //                     ...UserResource::getNavigationItems(),
        //                     ...RoleResource::getNavigationItems()->,
        //                 ])->collapsed()
        //         ])
        //         ->groups([
        //             NavigationGroup::make('masters')
        //                 ->items([
        //                     ...BankResource::getNavigationItems(),
        //                     ...BankAccountResource::getNavigationItems(),
        //                     ...VendorResource::getNavigationItems(),
        //                 ])->collapsed()
        //         ]);
        // });
    }
}
