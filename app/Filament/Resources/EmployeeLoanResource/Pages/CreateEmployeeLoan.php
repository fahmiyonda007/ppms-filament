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
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployeeLoan extends CreateRecord
{
    protected static string $resource = EmployeeLoanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
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

    protected function handleRecordCreation(array $data): Model
    {
        $coaSource = CoaThird::find($data['coa_id_source']);
        $coaDestination = CoaThird::find($data['coa_id_destination']);
        $employee = Employee::find($data['employee_id']);
        $data['nik'] = $employee->nik;
        $data['source_start_balance'] = (float)$coaSource->balance;
        $data['source_end_balance'] = (float)$coaSource->balance - (float)$data['amount'];
        $data['destination_start_balance'] = (float)$coaDestination->balance;
        $data['destination_end_balance'] = (float)$coaDestination->balance + (float)$data['amount'];

        $data['created_by'] = auth()->user()->email;
        return static::getModel()::create($data);
    }
}
