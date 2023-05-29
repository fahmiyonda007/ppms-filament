<?php

namespace App\Filament\Resources\VendorLiabilityResource\Pages;

use App\Filament\Resources\VendorLiabilityResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditVendorLiability extends EditRecord
{
    protected static string $resource = VendorLiabilityResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
        ];
    }

    protected function beforeFill(): void
    {
        // if ($this->record->is_jurnal == 1 || $this->record->vendorLiabilityPayments->count() > 0) {
        //     $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        // }
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
