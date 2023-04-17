<?php

namespace App\Filament\Resources\CoaFirstResource\Pages;

use App\Filament\Resources\CoaFirstResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCoaFirsts extends ListRecords
{
    protected static string $resource = CoaFirstResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Level 1'),
        ];
    }
}
