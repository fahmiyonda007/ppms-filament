<?php

namespace App\Filament\Resources\CashTransferResource\Pages;

use App\Filament\Resources\CashTransferResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashTransfer extends EditRecord
{
    protected static string $resource = CashTransferResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
