<?php

namespace App\Filament\Resources\VendorLiabilityResource\Pages;

use App\Filament\Resources\VendorLiabilityResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateVendorLiability extends CreateRecord
{
    protected static string $resource = VendorLiabilityResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by'] = auth()->user()->email;
        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
