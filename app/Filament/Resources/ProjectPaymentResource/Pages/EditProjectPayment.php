<?php

namespace App\Filament\Resources\ProjectPaymentResource\Pages;

use App\Filament\Resources\ProjectPaymentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProjectPayment extends EditRecord
{
    protected static string $resource = ProjectPaymentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }
}
