<?php

namespace App\Filament\Resources\ProjectPlanResource\Pages;

use App\Filament\Resources\ProjectPlanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProjectPlan extends EditRecord
{
    protected static string $resource = ProjectPlanResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }
}
