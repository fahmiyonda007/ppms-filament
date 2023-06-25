<?php

namespace App\Filament\Resources\CoaFirstResource\Pages;

use App\Filament\Resources\CoaFirstResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCoaFirst extends EditRecord
{
    protected static string $resource = CoaFirstResource::class;

    protected function getActions(): array
    {
        return [
            // Actions\DeleteAction::make()
            //     ->before(function ($record) {
            //         $record->seconds()->where('level_first_id', $record->id)->delete();
            //         $record->thirds()->where('level_first_id', $record->id)->delete();
            //     }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }

}
