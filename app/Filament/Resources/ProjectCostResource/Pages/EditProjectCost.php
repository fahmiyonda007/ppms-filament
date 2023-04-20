<?php

namespace App\Filament\Resources\ProjectCostResource\Pages;

use App\Filament\Resources\ProjectCostResource;
use App\Models\ProjectCostDetail;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditProjectCost extends EditRecord
{
    protected static string $resource = ProjectCostResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->email;
        $details = ProjectCostDetail::where('project_cost_id', $record->id);
        $details->each(function (ProjectCostDetail $det) {
            $det->amount = $det->unit_price * $det->qty;
            $det->save();
        });

        $data['total_amount'] = $details->sum('amount');
        $record->update($data);
        return $record;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $dt = $this->record->projectCostDetails->where('project_cost_id', $data['id']);
        $uniq = $dt->unique('coa_id');
        $not_unique =$dt->diff($uniq);
        // dd(count($not_unique));

        if (count($not_unique) > 0) {
            Notification::make()
                ->title('COA is duplicate')
                ->danger()
                ->send();

            throw ValidationException::withMessages(['COA is duplicate']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
