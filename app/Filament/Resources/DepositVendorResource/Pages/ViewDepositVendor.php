<?php

namespace App\Filament\Resources\DepositVendorResource\Pages;

use App\Filament\Resources\DepositVendorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDepositVendor extends ViewRecord
{
    protected static string $resource = DepositVendorResource::class;

    protected function getActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
