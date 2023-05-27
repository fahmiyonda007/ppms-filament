<?php

namespace App\Filament\Resources\EmployeePayrollResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\Common\JournalRepository;
use App\Filament\Resources\EmployeePayrollResource;
use App\Models\CoaThird;
use App\Models\Employee;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use App\Models\Receivable;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditEmployeePayroll extends EditRecord
{
    protected static string $resource = EmployeePayrollResource::class;

    protected $listeners = ['refresh' => '$refresh'];

    protected function getActions(): array
    {
        return [
            Actions\Action::make('post_jurnal')
                ->label('Post Journal')
                ->icon('heroicon-s-cash')
                ->action('postJournal')
                ->visible(function () {
                    $record = $this->record;
                    if ($record->is_jurnal == 0) {
                        return true;
                    }

                    return false;
                })
                ->requiresConfirmation()
                ->successNotificationMessage("Succesfully post to journal"),
            Actions\DeleteAction::make()
                ->visible(function () {
                    $record = $this->record;
                    if ($record->is_jurnal == 0) {
                        return true;
                    }

                    return false;
                }),
        ];
    }

    protected function beforeFill(): void
    {
        if ($this->record->is_jurnal == 1) {
            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }

    public function refreshForm()
    {
        $this->fillForm();
    }

    protected function afterValidate()
    {
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }

    public function postJournal()
    {
        $record = $this->record;

        if ((float) $record->employeePayrollDetails->count() == 0) {
            Notification::make()
                ->title('Input detail terlebih dahulu.')
                ->danger()
                ->send();
            $this->halt();
        }

        JournalRepository::PayrollJournal($this->record);
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
