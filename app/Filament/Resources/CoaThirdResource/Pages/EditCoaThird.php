<?php

namespace App\Filament\Resources\CoaThirdResource\Pages;

use App\Filament\Resources\CoaThirdResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCoaThird extends EditRecord
{
    protected static string $resource = CoaThirdResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
