<?php

namespace App\Filament\Resources\CashTransferResource\Pages;

use App\Filament\Resources\CashTransferResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCashTransfer extends EditRecord
{
    protected static string $resource = CashTransferResource::class;

    protected function getActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    // protected function beforeFill(): void
    // {
    //     $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
    // }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }
}
