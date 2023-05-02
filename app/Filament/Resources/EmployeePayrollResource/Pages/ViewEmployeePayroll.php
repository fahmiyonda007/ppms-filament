<?php

namespace App\Filament\Resources\EmployeePayrollResource\Pages;

use App\Filament\Resources\EmployeePayrollResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployeePayroll extends ViewRecord
{
    protected static string $resource = EmployeePayrollResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(function () {
                    $record = $this->record;
                    if ($record->is_jurnal == 0) {
                        return true;
                    }

                    return false;
                }),
        ];
    }
}
