<?php

namespace App\Filament\Resources\DepositVendorResource\Pages;

use App\Filament\Resources\DepositVendorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepositVendors extends ListRecords
{
    protected static string $resource = DepositVendorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
