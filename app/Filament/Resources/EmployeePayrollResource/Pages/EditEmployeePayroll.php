<?php

namespace App\Filament\Resources\EmployeePayrollResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\EmployeePayrollResource;
use App\Models\CoaThird;
use App\Models\Employee;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

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

        if ((float)$record->employeePayrollDetails->count() == 0) {
            Notification::make()
                ->title('Input detail terlebih dahulu.')
                ->danger()
                ->send();
            $this->halt();
        }

        $this->save();

        //jurnal penggajian
        $journal = GeneralJournal::create([
            "project_plan_id" => $record->project_plan_id,
            'jurnal_id' => Common::getNewJournalId(),
            'reference_code' => $record->transaction_code,
            'description' => 'jurnal penggajian - ' . $record->description,
            'transaction_date' => Carbon::now(),
            'created_by' => auth()->user()->email,
        ]);

        $coaThirdSource = CoaThird::find($record->coa_id_source);
        $coaThirdDestination = CoaThird::find($record->coa_id_destination);

        //Journal from coa source
        GeneralJournalDetail::create([
            'jurnal_id' => $journal->id,
            'no_inc' => 1,
            'coa_id' => $record->coa_id_source,
            'coa_code' => $coaThirdSource->code,
            'debet_amount' => 0,
            'credit_amount' => $record->payroll_total,
            'description' => $coaThirdSource->name,
        ]);

        //Journal from coa destination
        GeneralJournalDetail::create([
            'jurnal_id' => $journal->id,
            'no_inc' => 2,
            'coa_id' => $record->coa_id_destination,
            'coa_code' => $coaThirdDestination->code,
            'debet_amount' => $record->payroll_total,
            'credit_amount' => 0,
            'description' => $coaThirdDestination->name,
        ]);

        $coaThirdSource->balance = (float)$coaThirdSource->balance - (float)$record->payroll_total;
        $coaThirdSource->save();
        $coaThirdDestination->balance = (float)$coaThirdDestination->balance + (float)$record->payroll_total;
        $coaThirdDestination->save();

        //jurnal pembayaran kas bon
        if ($record->payment_loan_total > 0) {

            $journal = GeneralJournal::create([
                "project_plan_id" => $record->project_plan_id,
                'jurnal_id' => Common::getNewJournalId(),
                'reference_code' => $record->transaction_code,
                'description' => 'jurnal pembayaran kas bon - ' . $record->description,
                'transaction_date' => Carbon::now(),
                'created_by' => auth()->user()->email,
            ]);

            $coaThirdSource = CoaThird::find($record->coa_id_source);
            $coaThirdDestination = CoaThird::find($record->coa_id_loan);

            //Journal from coa source
            GeneralJournalDetail::create([
                'jurnal_id' => $journal->id,
                'no_inc' => 1,
                'coa_id' => $record->coa_id_source,
                'coa_code' => $coaThirdSource->code,
                'debet_amount' => $record->payment_loan_total,
                'credit_amount' => 0,
                'description' => $coaThirdSource->name,
            ]);

            //Journal from coa destination
            GeneralJournalDetail::create([
                'jurnal_id' => $journal->id,
                'no_inc' => 2,
                'coa_id' => $record->coa_id_destination,
                'coa_code' => $coaThirdDestination->code,
                'debet_amount' => 0,
                'credit_amount' => $record->payment_loan_total,
                'description' => $coaThirdDestination->name,
            ]);

            $coaThirdSource->balance = (float)$coaThirdSource->balance + (float)$record->payment_loan_total;
            $coaThirdSource->save();
            $coaThirdDestination->balance = (float)$coaThirdDestination->balance - (float)$record->payment_loan_total;
            $coaThirdDestination->save();

            // update total loan masing2 employee
            foreach ($record->employeePayrollDetails as $value) {
                $employee = Employee::find($value->employee_id);
                $employee->total_loan = $employee->total_loan - $record->loan_payment;
                $employee->save();
            }
        }

        $record->update([
            'is_jurnal' => 1,
            'updated_by' => auth()->user()->email
        ]);

        $this->redirect($this->getResource()::getUrl('index'));
    }
}
