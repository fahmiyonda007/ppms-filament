<?php

namespace App\Filament\Resources\EmployeePayrollResource\Pages;

use App\Filament\Resources\EmployeePayrollResource;
use Closure;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class ListEmployeePayrolls extends ListRecords
{
    protected static string $resource = EmployeePayrollResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn ($record) => $this->getResource()::getUrl('edit', ['record' => $record]);
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 20];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->fastPaginate($this->getTableRecordsPerPage());
    }
}
