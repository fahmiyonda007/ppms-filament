<?php

namespace App\Filament\Resources\CashFlowResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\CashFlowResource;
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

        $journal = GeneralJournal::create([
            "project_plan_id" => $record->project_plan_id,
            'jurnal_id' => Common::getNewJournalId(),
            'reference_code' => $record->transaction_code,
            'description' => $record->description,
            'transaction_date' => Carbon::now(),
            'created_by' => auth()->user()->email,
        ]);

        //Journal from header
        GeneralJournalDetail::create([
            'jurnal_id' => $journal->id,
            'no_inc' => 1,
            'coa_id' => $record->coa_id,
            'coa_code' => $coaThirdHeader->code,
            // 'debet_amount' => $record->cash_flow_type == 'SETOR_MODAL' ? $sumDetail : 0,
            // 'credit_amount' => $record->cash_flow_type == 'CASH_OUT' ? $sumDetail : 0,
            'debet_amount' => $record->cash_flow_type == 'CASH_IN' ? $sumDetail : 0,
            'credit_amount' => $record->cash_flow_type == 'CASH_OUT' || $record->cash_flow_type == 'SETOR_MODAL' ? $sumDetail : 0,
            'description' => $coaThirdHeader->name,
        ]);

        $coaThirdHeader->save();


        $countInc = 2;
        foreach ($record->cashFlowDetails as $key => $value) {
            $coaThirdDetail = CoaThird::find($value->coa_id);
            if ($record->cash_flow_type == 'SETOR_MODAL') {
                $coaThirdHeader_sm = CoaThird::find($value->coa_id);
                $coaThirdHeader_sm->balance = $coaThirdHeader_sm->balance + $value->amount;
                $coaThirdHeader_sm->save();
            }
            //Journal from detail
            GeneralJournalDetail::create([
                'jurnal_id' => $journal->id,
                'no_inc' => $countInc,
                'coa_id' => $value->coa_id,
                'coa_code' => $coaThirdDetail->code,
                'debet_amount' => $record->cash_flow_type == 'CASH_OUT' || $record->cash_flow_type == 'SETOR_MODAL' ? $value->amount : 0,
                'credit_amount' => $record->cash_flow_type == 'CASH_IN' ? $value->amount : 0,
                'description' => $coaThirdDetail->name,
            ]);

            // $coaThirdDetail->balance = $coaThirdDetail->balance + $value->amount;
            // $coaThirdDetail->save();

            $countInc = $countInc + 1;

        }

        $record->update([
            'is_jurnal' => 1,
            'updated_by' => auth()->user()->email
        ]);

        $this->redirect($this->getResource()::getUrl('index'));
    }
}
