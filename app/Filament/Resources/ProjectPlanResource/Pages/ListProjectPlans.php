<?php

namespace App\Filament\Resources\ProjectPlanResource\Pages;

use App\Filament\Resources\ProjectPlanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class ListProjectPlans extends ListRecords
{
    protected static string $resource = ProjectPlanResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 20];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->fastPaginate($this->getTableRecordsPerPage());
    }
    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery()->whereNotIn('id', [1, 2]);
    }
}