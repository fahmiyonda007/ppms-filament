<?php

namespace App\Filament\Resources\ProjectPaymentResource\Pages;

use App\Filament\Resources\Common\JournalRepository;
use App\Filament\Resources\ProjectPaymentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Notification;

class ViewProjectPayment extends ViewRecord
{
    protected static string $resource = ProjectPaymentResource::class;

    protected function getActions(): array
    {
        return [
            // Actions\Action::make('post_jurnal')
            //     ->label('Post Journal')
            //     ->icon('heroicon-s-cash')
            //     ->action('postJournal')
            //     ->visible(function () {
            //         return $this->record->is_jurnal == 0;
            //     })
            // ->requiresConfirmation(),
            Actions\EditAction::make()
                ->visible(function ($record) {
                    return $record->is_jurnal == 0 && $this->record->projectPaymentDetails->count() == 0;
                }),
        ];
    }

    public function postJournal()
    {
        $record = $this->record;

        if ((float) $record->projectPaymentDetails->count() == 0) {
            Notification::make()
                ->title('Input detail terlebih dahulu.')
                ->danger()
                ->send();
            $this->halt();
        }

        JournalRepository::ProjectPaymentPostJournal($this->record);
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
