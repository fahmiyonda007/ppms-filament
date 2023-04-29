<?php

namespace App\Filament\Resources\ProjectCostResource\Pages;

use App\Filament\Common\Common;
use App\Filament\Resources\ProjectCostResource;
use App\Models\CoaThird;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use App\Models\ProjectCost;
use App\Models\ProjectCostDetail;
use App\Models\Vendor;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class EditProjectCost extends EditRecord
{
    protected static string $resource = ProjectCostResource::class;
    protected $listeners = ['refresh' => '$refresh'];

    protected function getActions(): array
    {
        return [
            Actions\Action::make('set_to_paid')
                ->label('Set to PAID')
                ->icon('heroicon-s-cash')
                ->action('setToPaid')
                ->requiresConfirmation(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    public function refreshForm()
    {
        $this->fillForm();
    }

    protected function beforeFill(): void
    {
        if ($this->record->payment_status == 'PAID') {
            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $totAmt = $record->projectCostDetails->sum('amount');
        $sumPayment = $this->getSumPaymentSource($data);
        $data['total_amount'] = $totAmt;
        $data['total_payment'] = $sumPayment > $totAmt ? $totAmt : $sumPayment;
        $data['updated_by'] = auth()->user()->email;

        if ($totAmt == 0) {
            $data['payment_status'] = 'NOT PAID';
            $data['coa_id_source1'] = null;
            $data['coa_id_source2'] = null;
            $data['coa_id_source3'] = null;
        }

        $record->update($data);

        return $record;
    }

    // protected function getRedirectUrl(): string
    // {
    //     if ($this->record->payment_status == 'PAID') {
    //         return $this->getResource()::getUrl('view', ['record' => $this->record]);
    //     } else {
    //         return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    //     }
    // }

    public function setToPaid()
    {
        $record = $this->record;
        if ($record->payment_status == 'PAID') {
            Notification::make()
                ->title('Tidak Dapat melakukan proses ini jika data sudah PAID.')
                ->danger()
                ->send();
            $this->halt();
        }

        $this->save(false);
        $data = $record->toArray();

        $totPayment = (float)$data['total_payment'];
        $totAmount = (float)$data['total_amount'];

        if ($totPayment < $totAmount) {
            Notification::make()
                ->title('Pembayaran kurang dari Total Amount.')
                ->danger()
                ->send();
            $this->halt();
        }



        if ($totPayment >= $totAmount) {
            $sources = [
                $this->getSource1($data),
                $this->getSource2($data),
                $this->getSource3($data),
            ];

            $dataJournal = [
                "project_plan_id" => $record->project_plan_id,
                'jurnal_id' => Common::getNewJournalId(),
                'reference_code' => $record->transaction_code,
                'description' => $record->description,
                'transaction_date' => Carbon::now(),
                'created_by' => auth()->user()->email,
                // 'updated_by'=> null
            ];
            $journal = GeneralJournal::create($dataJournal);
            $countJournalDetails = 1;

            $totalAmount = (float)$data['total_amount'];
            foreach ($sources as $key => $value) {
                if ($value['id'] != 0) {
                    $paymentAmount = (float)$value['amount'];
                    $calcAmount = $totalAmount > $paymentAmount ? $paymentAmount : $totalAmount;
                    if ($totalAmount > 0) {
                        if ($value['table'] == 'vendors') {
                            $coa = CoaThird::where('name', Common::$depositToko)->first();
                            $coa->update(['balance' => (float)$coa->getOriginal('balance') - $calcAmount]);
                            if ($calcAmount > 0) {
                                GeneralJournalDetail::create([
                                    'jurnal_id' => $journal->id,
                                    'no_inc' => $countJournalDetails,
                                    'coa_id' => $coa->id,
                                    'coa_code' => $coa->code,
                                    'debet_amount' => 0,
                                    'credit_amount' => $calcAmount,
                                    'description' => $coa->name,
                                ]);
                            }
                        }
                        $qry = "update {$value['table']} set {$value['column']} = `{$value['column']}` - {$calcAmount} where id = {$value['id']}";
                        DB::statement((string)$qry);
                        $totalAmount = $totalAmount - $calcAmount;

                        if ($value['table'] != 'vendors') {
                            $coaForJournal = CoaThird::find($value['id']);
                            if ($calcAmount > 0) {
                                GeneralJournalDetail::create([
                                    'jurnal_id' => $journal->id,
                                    'no_inc' => $countJournalDetails,
                                    'coa_id' => $coaForJournal->id,
                                    'coa_code' => $coaForJournal->code,
                                    'debet_amount' => 0,
                                    'credit_amount' => $calcAmount,
                                    'description' => $coaForJournal->name,
                                ]);
                                $countJournalDetails = $countJournalDetails + 1;
                            }
                        }
                    }
                }
            }

            foreach ($record->projectCostDetails as $key => $value) {
                $coaForJournal = CoaThird::find($value->coa_id);
                if ($value->amount > 0) {
                    GeneralJournalDetail::create([
                        'jurnal_id' => $journal->id,
                        'no_inc' => $countJournalDetails,
                        'coa_id' => $coaForJournal->id,
                        'coa_code' => $coaForJournal->code,
                        'debet_amount' => $value->amount,
                        'credit_amount' => 0,
                        'description' => $coaForJournal->name,
                    ]);
                    $countJournalDetails = $countJournalDetails + 1;
                }
            }


            $data['payment_status'] = 'PAID';
            $record->update($data);

            $this->redirect($this->getResource()::getUrl('view', ['record' => $record]));
        }
    }

    protected function getSource1(array $data): array
    {
        $res = [
            "id" => 0,
            "table" => '',
            "column" => '',
            "amount" => 0,
            "method" => 'getSource1'
        ];
        $coaThird1 = 0;
        $coaThird = CoaThird::find($data['coa_id_source1']);
        if ($coaThird) {
            $cond = $coaThird->name == Common::$depositToko && $data['vendor_id'] != null;
            if ($cond) {
                $vendor = Vendor::find($data['vendor_id']);
                $coaThird1 = $vendor->deposit;
                $res['id'] = (int)$vendor->id;
                $res['table'] = 'vendors';
                $res['column'] = 'deposit';
            } else {
                $coaThird1 = $coaThird->balance;
                $res['id'] = (int)$coaThird->id;
                $res['table'] = 'coa_level_thirds';
                $res['column'] = 'balance';
            }
        }

        $res['amount'] = (float)$coaThird1;
        return $res;
    }

    protected function getSource2(array $data): array
    {
        $res = [
            "id" => 0,
            "table" => '',
            "amount" => 0,
            "method" => 'getSource2'
        ];

        $coaThird2 = CoaThird::find($data['coa_id_source2']);
        if ($coaThird2) {
            $res['id'] = (int)$coaThird2->id;
            $res['table'] = 'coa_level_thirds';
            $res['column'] = 'balance';
            $res['amount'] = (float)$coaThird2->balance ?? 0;
        }
        return $res;
    }

    protected function getSource3(array $data): array
    {
        $res = [
            "id" => 0,
            "table" => '',
            "amount" => 0,
            "method" => 'getSource3'
        ];

        $coaThird2 = CoaThird::find($data['coa_id_source3']);
        if ($coaThird2) {
            $res['id'] = (int)$coaThird2->id;
            $res['table'] = 'coa_level_thirds';
            $res['column'] = 'balance';
            $res['amount'] = (float)$coaThird2->balance ?? 0;
        }
        return $res;
    }

    protected function getSumPaymentSource(array $data): float
    {
        $coaThird1 = 0;
        $coaThird = CoaThird::find($data['coa_id_source1']);
        if ($coaThird) {
            $cond = $coaThird->name == Common::$depositToko && $data['vendor_id'] != null;
            if ($cond) {
                $vendor = Vendor::find($data['vendor_id']);
                $coaThird1 = $vendor->deposit;
            } else {
                $coaThird1 = $coaThird->balance;
            }
        }
        $coaThird2 = CoaThird::find($data['coa_id_source2'])->balance ?? 0;
        $coaThird3 = CoaThird::find($data['coa_id_source3'])->balance ?? 0;
        $sum = (float)$coaThird1 + (float)$coaThird2 + (float)$coaThird3;
        return $sum;
    }
}
