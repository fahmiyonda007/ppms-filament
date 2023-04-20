<?php

namespace App\Filament\Resources\SysLookupResource\Pages;

use App\Filament\Resources\SysLookupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSysLookup extends CreateRecord
{
    protected static string $resource = SysLookupResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
