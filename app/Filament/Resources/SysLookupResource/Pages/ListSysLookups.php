<?php

namespace App\Filament\Resources\SysLookupResource\Pages;

use App\Filament\Resources\SysLookupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSysLookups extends ListRecords
{
    protected static string $resource = SysLookupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
