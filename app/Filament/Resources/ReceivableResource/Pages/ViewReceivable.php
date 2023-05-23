<?php

namespace App\Filament\Resources\ReceivableResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\ReceivableResource;
use App\Filament\Resources\ReceivableResource\ReceivableRepository;
use App\Models\CoaThird;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReceivable extends ViewRecord
{
    protected static string $resource = ReceivableResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('post_jurnal')
                ->label('Post Journal')
                ->icon('heroicon-s-cash')
                ->action('postJournal')
                ->visible(function () {
                    return $this->record->is_jurnal == 0;
                })
                ->requiresConfirmation(),
            Actions\EditAction::make()
                ->visible(function ($record) {
                    return $record->is_jurnal == 0;
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $loan = EmployeeLoan::find($data['loan_id']);
        $data['employee_name'] = $loan->employee->employee_name;
        return $data;
    }

    public function postJournal()
    {
        ReceivableRepository::postJournal($this->record);
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
