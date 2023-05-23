<?php

namespace App\Filament\Resources\ReceivableResource\Pages;

use App\Filament\Resources\ReceivableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateReceivable extends CreateRecord
{
    protected static string $resource = ReceivableResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by'] = auth()->user()->email;
        return static::getModel()::create($data);
    }
}
