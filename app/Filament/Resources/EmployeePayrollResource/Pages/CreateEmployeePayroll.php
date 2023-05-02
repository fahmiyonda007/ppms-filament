<?php

namespace App\Filament\Resources\EmployeePayrollResource\Pages;

use App\Filament\Resources\EmployeePayrollResource;
use App\Models\CoaThird;
use App\Models\Employee;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployeePayroll extends CreateRecord
{
    protected static string $resource = EmployeePayrollResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function afterValidate()
    {

    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by'] = auth()->user()->email;
        return static::getModel()::create($data);
    }
}
