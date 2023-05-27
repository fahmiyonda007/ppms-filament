<?php

namespace App\Filament\Resources\DepositVendorResource\Pages;

use App\Filament\Resources\Common\JournalRepository;
use App\Filament\Resources\DepositVendorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDepositVendor extends ViewRecord
{
    protected static string $resource = DepositVendorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('post_jurnal')
                ->label('Post Journal')
                ->icon('heroicon-s-cash')
                ->action('postJournal')
                ->visible(function () {
                    return $this->record->is_jurnal == 0;
                })
                ->requiresConfirmation(),
            Actions\EditAction::make()
                ->visible(function ($record) {
                    return $record->is_jurnal == 0;
                }),
        ];
    }

    public function postJournal()
    {
        JournalRepository::DepositVendorJournal($this->record);
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
