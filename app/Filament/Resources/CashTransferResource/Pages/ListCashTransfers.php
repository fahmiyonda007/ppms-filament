<?php

namespace App\Filament\Resources\CashTransferResource\Pages;

use App\Filament\Resources\CashTransferResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashTransfers extends ListRecords
{
    protected static string $resource = CashTransferResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
