<?php

namespace App\Filament\Resources\CashFlowResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\CashFlowResource;
use App\Filament\Resources\Common\JournalRepository;
use App\Models\CoaThird;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewCashFlow extends ViewRecord
{
    protected static string $resource = CashFlowResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('post_jurnal')
                ->label('Post Journal')
                ->icon('heroicon-s-cash')
                ->action('postJournal')
                ->visible(function () {
                    $record = $this->record;
                    if ($record->is_jurnal == 0 && $record->cashFlowDetails->count() > 0) {
                        return true;
                    }

                    return false;
                })
                ->requiresConfirmation(),
            Actions\EditAction::make()
                ->visible(function ($record) {
                    if ($record->cashFlowDetails->count() > 0) {
                        return false;
                    }
                    return true;
                }),
        ];
    }

    public function postJournal()
    {
        $record = $this->record;
        $coaThirdHeader = CoaThird::find($record->coa_id);

        $sumDetail = $record->cashFlowDetails->sum('amount');

        if ($record->cash_flow_type == 'SETOR_MODAL') {
            $coaThirdHeader->balance = $coaThirdHeader->balance + $sumDetail;
        } else {
            $coaThirdHeader->balance = $coaThirdHeader->balance - $sumDetail;

            if ($coaThirdHeader->balance < 0) {
                Notification::make()
                    ->title('COA Header Balance kurang.')
                    ->danger()
                    ->send();
                $this->halt();
            }
        }

        JournalRepository::CashFlowJournal($record, $coaThirdHeader, $sumDetail);

        $this->redirect($this->getResource()::getUrl('index'));
    }
}
