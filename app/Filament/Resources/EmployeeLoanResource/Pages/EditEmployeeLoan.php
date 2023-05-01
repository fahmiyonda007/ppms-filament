<?php

namespace App\Filament\Resources\EmployeeLoanResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\EmployeeLoanResource;
use App\Models\CoaThird;
use App\Models\Employee;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditEmployeeLoan extends EditRecord
{
    protected static string $resource = EmployeeLoanResource::class;

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

    protected function afterValidate()
    {
        $data = $this->data;

        $coaSource = (float)CoaThird::find($data['coa_id_source'])->balance;
        $data['source_end_balance'] = $coaSource - (float)$data['amount'];

        if ((float)$data['source_end_balance'] < 0) {
            Notification::make()
                ->title('Source End Balance tidak boleh < 0.')
                ->danger()
                ->send();
            $this->halt();
        }
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $coaSource = CoaThird::find($data['coa_id_source']);
        $coaDestination = CoaThird::find($data['coa_id_destination']);
        $employee = Employee::find($data['employee_id']);
        $data['nik'] = $employee->nik;
        $data['source_start_balance'] = (float)$coaSource->balance;
        $data['source_end_balance'] = (float)$coaSource->balance - (float)$data['amount'];
        $data['destination_start_balance'] = (float)$coaDestination->balance;
        $data['destination_end_balance'] = (float)$coaDestination->balance + (float)$data['amount'];

        $data['updated_by'] = auth()->user()->email;
        $record->update($data);
        return $record;
    }

    public function postJournal()
    {
        $this->save();

        $record = $this->record;
        if ((float)$record->source_end_balance < 0) {
            Notification::make()
                ->title('Source End Balance tidak boleh < 0.')
                ->danger()
                ->send();
            $this->halt();
        }

        $journal = GeneralJournal::create([
            "project_plan_id" => $record->project_plan_id,
            'jurnal_id' => Common::getNewJournalId(),
            'reference_code' => $record->transaction_code,
            'description' => $record->description,
            'transaction_date' => Carbon::now(),
            'created_by' => auth()->user()->email,
        ]);

        $coaThirdSource = CoaThird::find($record->coa_id_source);
        $coaThirdDestination = CoaThird::find($record->coa_id_destination);

        //Journal credit from coa source
        GeneralJournalDetail::create([
            'jurnal_id' => $journal->id,
            'no_inc' => 1,
            'coa_id' => $record->coa_id_source,
            'coa_code' => $coaThirdSource->code,
            'debet_amount' => 0,
            'credit_amount' => $record->amount,
            'description' => $coaThirdSource->name,
        ]);

        //Journal credit from coa destination
        GeneralJournalDetail::create([
            'jurnal_id' => $journal->id,
            'no_inc' => 2,
            'coa_id' => $record->coa_id_destination,
            'coa_code' => $coaThirdDestination->code,
            'debet_amount' => $record->amount,
            'credit_amount' => 0,
            'description' => $coaThirdDestination->name,
        ]);

        $coaThirdSource->balance = (float)$coaThirdSource->balance - (float)$record->amount;
        $coaThirdSource->save();
        $coaThirdDestination->balance = (float)$coaThirdDestination->balance + (float)$record->amount;
        $coaThirdDestination->save();

        $employee = Employee::find($record->employee_id);
        $employee->total_loan = $employee->total_loan + $record->amount;
        $employee->save();

        $record->update([
            'is_jurnal' => 1,
            'updated_by' => auth()->user()->email
        ]);

        $this->redirect($this->getResource()::getUrl('index'));
    }
}
