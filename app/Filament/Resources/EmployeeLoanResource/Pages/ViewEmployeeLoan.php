<?php

namespace App\Filament\Resources\EmployeeLoanResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\Common\JournalRepository;
use App\Filament\Resources\EmployeeLoanResource;
use App\Models\CoaThird;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployeeLoan extends ViewRecord
{
    protected static string $resource = EmployeeLoanResource::class;

    protected function getActions(): array
    {
        return [
            // Actions\Action::make('post_jurnal')
            //     ->label('Post Journal')
            //     ->icon('heroicon-s-cash')
            //     ->action('postJournal')
            //     ->visible(function () {
            //         $record = $this->record;
            //         if ($record->is_jurnal == 0) {
            //             return true;
            //         }

            //         return false;
            //     })
            // ->requiresConfirmation()
            // ->successNotificationMessage("Succesfully post to journal"),
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

    public function postJournal()
    {
        $record = $this->record;
        $coaSource = (float)CoaThird::find($record->coa_id_source)->balance;
        $source_end_balance = $coaSource - (float)$record->amount;

        if ((float)$source_end_balance < 0) {
            Notification::make()
                ->title('Source End Balance tidak boleh < 0.')
                ->danger()
                ->send();
            $this->halt();
        }

        $this->save();

        JournalRepository::LoanJournal($this->record);
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
