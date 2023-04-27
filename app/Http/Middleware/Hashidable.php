<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Hashidable
{
    private $routeModelMapping = [
        // 'filament.resources.users.edit' => \App\Models\User::class,
        // 'filament.resources.users.view' => \App\Models\User::class,
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->route()->getName(), array_keys($this->routeModelMapping))) {
            $request->route()->setParameter('record', $this->getModelId($this->routeModelMapping[$request->route()->getName()], $request->route('record')));
        }
        return $next($request);
    }

    private function getModelId($model, $routeKey)
    {
        return \Hashids::connection($model)->decode($routeKey)[0] ?? $routeKey;
    }
}
