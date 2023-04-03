<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UserOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $orderData = Trend::model(User::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            Card::make('Users', User::count()),
            Card::make('Verified', User::where('verified', '1')->count())
                ->chart(
                    $orderData
                        ->map(fn (TrendValue $value) => $value->aggregate)
                        ->toArray()
                )
                ->chartColor('success')
                ->descriptionIcon('heroicon-o-trending-up')
                ->description($orderData->count() . ' Increases ' . Carbon::now()->monthName)
                ->descriptionColor('success')
        ];
    }
}
