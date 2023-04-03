<?php

namespace App\Providers;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\UserResource;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\UserMenuItem;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
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
        //                     ...RoleResource::getNavigationItems(),
        //                     ...PermissionResource::getNavigationItems()
        //                 ])
        //         ]);
        // });
    }
}
