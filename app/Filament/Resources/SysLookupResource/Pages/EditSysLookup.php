<?php

namespace App\Filament\Resources\SysLookupResource\Pages;

use App\Filament\Resources\SysLookupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSysLookup extends EditRecord
{
    protected static string $resource = SysLookupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
