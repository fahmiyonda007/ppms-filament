<?php

namespace App\Http\Middleware;

use App\Filament\Resources\UserResource;
use Closure;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMenuItemMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Filament::registerUserMenuItems([
            UserMenuItem::make()
                ->label('Settings')
                ->url(UserResource::getUrl('profile', ['record' => auth()->id()]))
                ->icon('heroicon-s-cog'),
        ]);
        return $next($request);
    }
}
