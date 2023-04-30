<?php

namespace App\Filament\Resources\DepositVendorResource\Pages;

use App\Filament\Resources\DepositVendorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDepositVendor extends EditRecord
{
    protected static string $resource = DepositVendorResource::class;

    protected function getActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }
}
