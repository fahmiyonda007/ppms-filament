<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = '2';

    protected function getCards(): array
    {
        return [
            // Card::make('Spotlight (Ctrl+K / Cmd+K)', '')
            //     ->description('')
            //     ->descriptionIcon('heroicon-s-trending-up')
            //     // ->chart([7, 2, 10, 3, 15, 4, 17])
            //     // ->color('success')
            //     ->extraAttributes([
            //         'class' => 'cursor-pointer',
            //         // 'wire:click' => '$emitUp("setStatusFilter", "processed")',
            //     ]),


        ];
    }
}
