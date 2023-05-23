<?php

namespace App\Filament\Resources\ReceivableResource;

use App\Filament\Common\Common;
use App\Models\CoaThird;
use App\Models\Employee;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use Carbon\Carbon;

class ReceivableRepository
{
    public static function postJournal($record)
    {
        $journal = GeneralJournal::create([
            // "project_plan_id" => $record->project_plan_id,
            'jurnal_id' => Common::getNewJournalId(),
            'reference_code' => $record->loan->transaction_code,
            'description' => '[jurnal penerimaan] ' . $record->description,
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
            'credit_amount' => $record->payment_amount,
            'description' => $coaThirdSource->name,
        ]);

        //Journal credit from coa destination
        GeneralJournalDetail::create([
            'jurnal_id' => $journal->id,
            'no_inc' => 2,
            'coa_id' => $record->coa_id_destination,
            'coa_code' => $coaThirdDestination->code,
            'debet_amount' => $record->payment_amount,
            'credit_amount' => 0,
            'description' => $coaThirdDestination->name,
        ]);

        $coaThirdSource->balance = (float)$coaThirdSource->balance - (float)$record->payment_amount;
        $coaThirdSource->save();
        $coaThirdDestination->balance = (float)$coaThirdDestination->balance + (float)$record->payment_amount;
        $coaThirdDestination->save();

        $employee = Employee::find($record->loan->employee_id);
        $employee->total_loan = $record->outstanding;
        $employee->save();

        $record->update([
            'is_jurnal' => 1,
            'updated_by' => auth()->user()->email
        ]);
    }
}
