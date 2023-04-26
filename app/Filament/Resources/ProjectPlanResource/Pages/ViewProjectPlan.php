<?php

namespace App\Filament\Resources\ProjectPlanResource\Pages;

use App\Filament\Resources\ProjectPlanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewProjectPlan extends ViewRecord
{
    protected static string $resource = ProjectPlanResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(function (Model $record) {
                    return $record->progress < 100.0;
                }),
        ];
    }
}
