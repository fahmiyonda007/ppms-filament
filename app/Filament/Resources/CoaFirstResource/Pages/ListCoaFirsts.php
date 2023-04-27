<?php

namespace App\Filament\Resources\CoaFirstResource\Pages;

use App\Filament\Resources\CoaFirstResource;
use App\Models\CoaFirst;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class ListCoaFirsts extends ListRecords
{
    protected static string $resource = CoaFirstResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New COA'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $coas = CoaFirst::selectRaw(
            "coa_level_firsts.*
            ,CONCAT_WS(' - ', coa_level_firsts.code, coa_level_firsts.name) as first
            ,CONCAT_WS(' - ', coa_level_seconds.code, coa_level_seconds.name) as second
            ,CONCAT_WS(' - ', coa_level_thirds.code, coa_level_thirds.name) as third"
        )
            ->join('coa_level_seconds', 'coa_level_firsts.id', '=', 'coa_level_seconds.level_first_id')
            ->join('coa_level_thirds', function ($join) {
                $join->on('coa_level_firsts.id', '=', 'coa_level_thirds.level_first_id');
                $join->on('coa_level_seconds.id', '=', 'coa_level_thirds.level_second_id');
            });
        // dd(($coas));
        return $coas;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 15, 25, 50];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->fastPaginate($this->getTableRecordsPerPage());
    }
}
