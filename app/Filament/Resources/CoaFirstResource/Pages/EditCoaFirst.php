<?php

namespace App\Filament\Resources\CoaFirstResource\Pages;

use App\Filament\Resources\CoaFirstResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCoaFirst extends EditRecord
{
    protected static string $resource = CoaFirstResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
