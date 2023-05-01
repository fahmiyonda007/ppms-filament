<?php

namespace App\Filament\Resources\EmployeeLoanResource\Pages;

use App\Filament\Common\Common;
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

        $journal = GeneralJournal::create([
            "project_plan_id" => $record->project_plan_id,
            'jurnal_id' => Common::getNewJournalId(),
            'reference_code' => $record->transaction_id,
            'description' => $record->description,
            'transaction_date' => Carbon::now(),
            'created_by' => auth()->user()->email,
        ]);

        $coaThirdSource = CoaThird::find($record->coa_id_source);
        $coaThirdDestination = CoaThird::find($record->coa_id_destination);

        //Journal credit from coa source
        GeneralJournalDetail::create([
            'jurnal_id' => $journal->id,
            'no_inc' => 1,
            'coa_id' => $record->coa_id_source,
            'coa_code' => $coaThirdSource->code,
            'debet_amount' => 0,
            'credit_amount' => $record->amount,
            'description' => $coaThirdSource->name,
        ]);

        //Journal credit from coa destination
        GeneralJournalDetail::create([
            'jurnal_id' => $journal->id,
            'no_inc' => 2,
            'coa_id' => $record->coa_id_destination,
            'coa_code' => $coaThirdDestination->code,
            'debet_amount' => $record->amount,
            'credit_amount' => 0,
            'description' => $coaThirdDestination->name,
        ]);

        $coaThirdSource->balance = (float)$coaThirdSource->balance - (float)$record->amount;
        $coaThirdSource->save();
        $coaThirdDestination->balance = (float)$coaThirdDestination->balance + (float)$record->amount;
        $coaThirdDestination->save();

        $record->update([
            'is_jurnal' => 1,
            'updated_by' => auth()->user()->email
        ]);

        $this->redirect($this->getResource()::getUrl('index'));
    }
}
