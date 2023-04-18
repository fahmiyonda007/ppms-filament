<?php

namespace App\Filament\Resources\ProjectCostResource\Pages;

use App\Filament\Resources\ProjectCostResource;
use App\Models\ProjectCostDetail;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
        ProjectCostDetail::where('project_cost_id', $record->id)
            ->each(function (ProjectCostDetail $det) {
                $det->amount = $det->unit_price * $det->qty;
                $det->save();
            });
        // dd($record);
        $record->update($data);
        return $record;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
