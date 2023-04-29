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
            Actions\Action::make('Export Pdf')->button()
                ->url(fn () => route('plan-pdf', ['record' => $this->record]))
                ->openUrlInNewTab(),
            Actions\Action::make('Export Excel')->button()
                ->url(fn () => route('plan-excel', ['record' => $this->record]))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }

    protected function beforeFill(): void
    {
        if ($this->record->progress == 100) {
            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }
}
