<?php

namespace App\Filament\Resources\VendorLiabilityResource\Pages;

use App\Filament\Resources\VendorLiabilityResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVendorLiability extends ViewRecord
{
    protected static string $resource = VendorLiabilityResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                // ->visible(function ($record) {
                //     return $record->vendorLiabilityPayments->count() == 0;
                // }),
        ];
    }
}
