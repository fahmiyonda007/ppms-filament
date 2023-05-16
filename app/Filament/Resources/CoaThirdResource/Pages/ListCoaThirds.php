<?php

namespace App\Filament\Resources\CoaThirdResource\Pages;

use App\Filament\Resources\CoaFirstResource;
use App\Filament\Resources\CoaThirdResource;
use Closure;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ListCoaThirds extends ListRecords
{
    protected static string $resource = CoaThirdResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(fn (): string => CoaFirstResource::getUrl('create')),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn ($record) => CoaFirstResource::getUrl('index') . '/' . $record->level_first_id . '/edit?activeRelationManager=1';
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
