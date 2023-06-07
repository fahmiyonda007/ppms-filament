<?php

namespace App\Filament\Resources\Common;

use App\Filament\Common\Common;
use App\Models\CoaThird;
use App\Models\Employee;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use App\Models\Receivable;
use App\Models\VendorLiabilityPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JournalRepository
{
    public static function ReceivablePostJournal($record)
    {
        DB::transaction(function () use ($record) {

            $coaThirdSource = CoaThird::find($record->coa_id_source);
            $coaThirdDestination = CoaThird::find($record->coa_id_destination);

            $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->payment_amount;
            $coaThirdSource->save();
            $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->payment_amount;
            $coaThirdDestination->save();

            $journal = GeneralJournal::create([
                // "project_plan_id" => $record->project_plan_id,
                'jurnal_id' => Common::getNewJournalId(),
                'reference_code' => $record->transaction_code,
                'description' => '[jurnal penerimaan] ' . $record->description,
                'transaction_date' => Carbon::now(),
                'created_by' => auth()->user()->email,
            ]);

            // $coaThirdSource = CoaThird::find($record->coa_id_source);
            // $coaThirdDestination = CoaThird::find($record->coa_id_destination);

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

            // $coaThirdSource->balance = (float)$coaThirdSource->balance - (float)$record->payment_amount;
            // $coaThirdSource->save();
            // $coaThirdDestination->balance = (float)$coaThirdDestination->balance + (float)$record->payment_amount;
            // $coaThirdDestination->save();

            $employee = Employee::find($record->employee_id);
            $employee->total_loan = $record->outstanding;
            $employee->save();

            $record->update([
                'is_jurnal' => 1,
                'updated_by' => auth()->user()->email
            ]);
        });
    }

    public static function ProjectPaymentPostJournal($record)
    {
        DB::transaction(function () use ($record) {
            //jurnal payment
            $journal = GeneralJournal::create([
                "project_plan_id" => $record->project_plan_id,
                'jurnal_id' => Common::getNewJournalId(),
                'reference_code' => $record->transaction_code,
                'description' => '[jurnal payment] ' . $record->description,
                'transaction_date' => Carbon::now(),
                'created_by' => auth()->user()->email,
            ]);

            $countLoop = 0;
            foreach ($record->projectPaymentDetails as $key => $value) {
                $coaThirdSource = CoaThird::find($value->coa_id_source);
                $coaThirdDestination = CoaThird::find($value->coa_id_destination);

                $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $value->amount;
                $coaThirdSource->save();
                $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $value->amount;
                $coaThirdDestination->save();

                $countLoop = $countLoop + 1;
                //Journal from coa source
                GeneralJournalDetail::create([
                    'jurnal_id' => $journal->id,
                    'no_inc' => $countLoop,
                    'coa_id' => $value->coa_id_source,
                    'coa_code' => $coaThirdSource->code,
                    'debet_amount' => 0,
                    'credit_amount' => $value->amount,
                    'description' => $coaThirdSource->name,
                ]);

                $countLoop = $countLoop + 1;
                //Journal from coa destination
                GeneralJournalDetail::create([
                    'jurnal_id' => $journal->id,
                    'no_inc' => $countLoop,
                    'coa_id' => $value->coa_id_destination,
                    'coa_code' => $coaThirdDestination->code,
                    'debet_amount' => $value->amount,
                    'credit_amount' => 0,
                    'description' => $coaThirdDestination->name,
                ]);

                // $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $value->amount;
                // $coaThirdSource->save();
                // $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $value->amount;
                // $coaThirdDestination->save();
            };

            $record->update([
                'is_jurnal' => 1,
                'updated_by' => auth()->user()->email
            ]);
        });
    }

    public static function ProjectPaymentDetailPostJournal($record, $header)
    {
        DB::transaction(function () use ($record, $header) {
            //jurnal payment
            $journal = GeneralJournal::create([
                "project_plan_id" => $header->project_plan_id,
                'jurnal_id' => Common::getNewJournalId(),
                'reference_code' => $record->transaction_code,
                'description' => "[jurnal payment] - [{$record->category} {$record->inc}] {$header->description}",
                'transaction_date' => Carbon::now(),
                'created_by' => auth()->user()->email,
            ]);

            $coaThirdSource = CoaThird::find($record->coa_id_source);
            $coaThirdDestination = CoaThird::find($record->coa_id_destination);

            $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->amount;
            $coaThirdSource->save();
            $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->amount;
            $coaThirdDestination->save();

            //Journal from coa source
            GeneralJournalDetail::create([
                'jurnal_id' => $journal->id,
                'no_inc' => 1,
                'coa_id' => $record->coa_id_source,
                'coa_code' => $coaThirdSource->code,
                'debet_amount' => 0,
                'credit_amount' => $record->amount,
                'description' => $coaThirdSource->name,
            ]);

            //Journal from coa destination
            GeneralJournalDetail::create([
                'jurnal_id' => $journal->id,
                'no_inc' => 2,
                'coa_id' => $record->coa_id_destination,
                'coa_code' => $coaThirdDestination->code,
                'debet_amount' => $record->amount,
                'credit_amount' => 0,
                'description' => $coaThirdDestination->name,
            ]);

            // $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->amount;
            // $coaThirdSource->save();
            // $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->amount;
            // $coaThirdDestination->save();

            $record->update([
                'is_jurnal' => 1,
                'updated_by' => auth()->user()->email
            ]);
        });
    }

    public static function VendorLiabilityPaymentPostJournal($record, $header)
    {
        DB::transaction(function () use ($record, $header) {
            //jurnal payment
            $journal = GeneralJournal::create([
                "project_plan_id" => $header->project_plan_id,
                'jurnal_id' => Common::getNewJournalId(),
                'reference_code' => $record->transaction_code,
                'description' => "[jurnal vendor liability] - [{$record->category} {$record->inc}] {$header->description}",
                'transaction_date' => Carbon::now(),
                'created_by' => auth()->user()->email,
            ]);

            $coaThirdSource = CoaThird::find($record->coa_id_source);
            $coaThirdDestination = CoaThird::find($record->coa_id_destination);

            $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->amount;
            $coaThirdSource->save();
            $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->amount;
            $coaThirdDestination->save();

            //Journal from coa source
            GeneralJournalDetail::create([
                'jurnal_id' => $journal->id,
                'no_inc' => 1,
                'coa_id' => $record->coa_id_source,
                'coa_code' => $coaThirdSource->code,
                'debet_amount' => 0,
                'credit_amount' => $record->amount,
                'description' => $coaThirdSource->name,
            ]);

            //Journal from coa destination
            GeneralJournalDetail::create([
                'jurnal_id' => $journal->id,
                'no_inc' => 2,
                'coa_id' => $record->coa_id_destination,
                'coa_code' => $coaThirdDestination->code,
                'debet_amount' => $record->amount,
                'credit_amount' => 0,
                'description' => $coaThirdDestination->name,
            ]);

            // $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->amount;
            // $coaThirdSource->save();
            // $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->amount;
            // $coaThirdDestination->save();

            $record->update([
                'is_jurnal' => 1,
                'updated_by' => auth()->user()->email
            ]);

            $checkDetail = VendorLiabilityPayment::where([
                ['vendor_liabilities_id', '=', $header->id],
                ['is_jurnal', '=', 0],
            ])->get();

            if ($checkDetail->count() == 0 && (float) $header->outstanding == 0) {
                $header->update([
                    'project_status' => 1
                ]);
            }
        });
    }

    public static function CashFlowJournal($record, $coaThirdHeader, $sumDetail)
    {
        DB::transaction(function () use ($record, $coaThirdHeader, $sumDetail) {

            $coaThirdHeader->save();

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
        });
    }

    public static function CashTransferJournal($record)
    {
        DB::transaction(function () use ($record) {
            $journal = GeneralJournal::create([
                "project_plan_id" => $record->project_plan_id,
                'jurnal_id' => Common::getNewJournalId(),
                'reference_code' => $record->transaction_id,
                'description' => '[jurnal cash transfer] ' . $record->description,
                'transaction_date' => Carbon::now(),
                'created_by' => auth()->user()->email,
            ]);

            $coaThirdSource = CoaThird::find($record->coa_id_source);
            $coaThirdDestination = CoaThird::find($record->coa_id_destination);

            $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->amount;
            $coaThirdSource->save();
            $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->amount;
            $coaThirdDestination->save();
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

            // $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->amount;
            // $coaThirdSource->save();
            // $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->amount;
            // $coaThirdDestination->save();

            $record->update([
                'is_jurnal' => 1,
                'updated_by' => auth()->user()->email
            ]);
        });
    }

    public static function ProjectPlanDetailPostJournal($record, $header)
    {
        $mapping = [
            ['code' => '501003', 'amount' => $record->notary_fee, 'desc' => 'BIAYA NOTARIS'],
            ['code' => '501004', 'amount' => $record->tax, 'desc' => 'BIAYA PAJAK'],
            ['code' => '505006', 'amount' => $record->commission, 'desc' => 'BIAYA KOMISI AGENT'],
            ['code' => '505007', 'amount' => $record->other_commission, 'desc' => 'BIAYA KOMISI AGENT LAINNYA'],
        ];

        DB::transaction(function () use ($mapping, $record, $header) {
            foreach ($mapping as $key => $value) {
                $journal = GeneralJournal::create([
                    "project_plan_id" => $header->id,
                    'jurnal_id' => Common::getNewJournalId(),
                    'reference_code' => $header->code,
                    'description' => "[jurnal plan] - {$record->unit_kavling} {$value['desc']}",
                    'transaction_date' => Carbon::now(),
                    'created_by' => auth()->user()->email,
                ]);

                $coaThirdSource = CoaThird::find($record->coa_id_source);
                $coaThirdDestination = CoaThird::where('code', $value['code'])->first();

                $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $value['amount'];
                $coaThirdSource->save();
                $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $value['amount'];
                $coaThirdDestination->save();

                //Journal credit from coa source
                GeneralJournalDetail::create([
                    'jurnal_id' => $journal->id,
                    'no_inc' => 1,
                    'coa_id' => $record->coa_id_source,
                    'coa_code' => $coaThirdSource->code,
                    'debet_amount' => 0,
                    'credit_amount' => $value['amount'],
                    'description' => $coaThirdSource->name,
                ]);

                //Journal credit from coa destination
                GeneralJournalDetail::create([
                    'jurnal_id' => $journal->id,
                    'no_inc' => 2,
                    'coa_id' => $coaThirdDestination->id,
                    'coa_code' => $coaThirdDestination->code,
                    'debet_amount' => $value['amount'],
                    'credit_amount' => 0,
                    'description' => $coaThirdDestination->name,
                ]);

                // $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $value['amount'];
                // $coaThirdSource->save();
                // $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $value['amount'];
                // $coaThirdDestination->save();
            }

            $record->update([
                'is_jurnal' => 1,
                'updated_by' => auth()->user()->email
            ]);
        });
    }

    public static function DepositVendorJournal($record)
    {
        DB::transaction(function () use ($record) {
            $journal = GeneralJournal::create([
                "project_plan_id" => $record->project_plan_id,
                'jurnal_id' => Common::getNewJournalId(),
                'reference_code' => $record->transaction_code,
                'description' => '[jurnal deposit vendor] ' . $record->description,
                'transaction_date' => Carbon::now(),
                'created_by' => auth()->user()->email,
            ]);

            $coaThirdSource = CoaThird::find($record->coa_id_source);
            $coaThirdDestination = CoaThird::find($record->coa_id_destination);

            $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->amount;
            $coaThirdSource->save();
            $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->amount;
            $coaThirdDestination->save();

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

            // $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->amount;
            // $coaThirdSource->save();
            // $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->amount;
            // $coaThirdDestination->save();

            $record->update([
                'is_jurnal' => 1,
                'updated_by' => auth()->user()->email
            ]);
        });
    }

    public static function LoanJournal($record)
    {
        DB::transaction(function () use ($record) {
            $journal = GeneralJournal::create([
                "project_plan_id" => $record->project_plan_id,
                'jurnal_id' => Common::getNewJournalId(),
                'reference_code' => $record->transaction_code,
                'description' => '[jurnal loan] ' . $record->description,
                'transaction_date' => Carbon::now(),
                'created_by' => auth()->user()->email,
            ]);

            $coaThirdSource = CoaThird::find($record->coa_id_source);
            $coaThirdDestination = CoaThird::find($record->coa_id_destination);

            $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->amount;
            $coaThirdSource->save();
            $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->amount;
            $coaThirdDestination->save();

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

            // $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->amount;
            // $coaThirdSource->save();
            // $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->amount;
            // $coaThirdDestination->save();

            $employee = Employee::find($record->employee_id);
            $employee->total_loan = $employee->total_loan + $record->amount;
            $employee->save();

            $record->update([
                'is_jurnal' => 1,
                'updated_by' => auth()->user()->email
            ]);
        });
    }

    public static function PayrollJournal($record)
    {
        DB::transaction(function () use ($record) {
            //jurnal penggajian
            $journal = GeneralJournal::create([
                "project_plan_id" => $record->project_plan_id,
                'jurnal_id' => Common::getNewJournalId(),
                'reference_code' => $record->transaction_code,
                'description' => '[jurnal penggajian] ' . $record->description,
                'transaction_date' => Carbon::now(),
                'created_by' => auth()->user()->email,
            ]);

            $coaThirdSource = CoaThird::find($record->coa_id_source);
            $coaThirdDestination = CoaThird::find($record->coa_id_destination);

            $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->payroll_total;
            $coaThirdSource->save();
            $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->payroll_total;
            $coaThirdDestination->save();

            //Journal from coa source
            GeneralJournalDetail::create([
                'jurnal_id' => $journal->id,
                'no_inc' => 1,
                'coa_id' => $record->coa_id_source,
                'coa_code' => $coaThirdSource->code,
                'debet_amount' => 0,
                'credit_amount' => $record->payroll_total,
                'description' => $coaThirdSource->name,
            ]);

            //Journal from coa destination
            GeneralJournalDetail::create([
                'jurnal_id' => $journal->id,
                'no_inc' => 2,
                'coa_id' => $record->coa_id_destination,
                'coa_code' => $coaThirdDestination->code,
                'debet_amount' => $record->payroll_total,
                'credit_amount' => 0,
                'description' => $coaThirdDestination->name,
            ]);

            // $coaThirdSource->balance = (float) $coaThirdSource->balance - (float) $record->payroll_total;
            // $coaThirdSource->save();
            // $coaThirdDestination->balance = (float) $coaThirdDestination->balance + (float) $record->payroll_total;
            // $coaThirdDestination->save();

            //jurnal pembayaran kas bon
            if ($record->payment_loan_total > 0) {

                $journal = GeneralJournal::create([
                    "project_plan_id" => $record->project_plan_id,
                    'jurnal_id' => Common::getNewJournalId(),
                    'reference_code' => $record->transaction_code,
                    'description' => '[jurnal pembayaran kas bon] ' . $record->description,
                    'transaction_date' => Carbon::now(),
                    'created_by' => auth()->user()->email,
                ]);

                $coaThirdSource = CoaThird::find($record->coa_id_source);
                $coaThirdDestination = CoaThird::find($record->coa_id_loan);

                $coaThirdSource->balance = (float) $coaThirdSource->balance + (float) $record->payment_loan_total;
                $coaThirdSource->save();
                $coaThirdDestination->balance = (float) $coaThirdDestination->balance - (float) $record->payment_loan_total;
                $coaThirdDestination->save();

                //Journal from coa source
                GeneralJournalDetail::create([
                    'jurnal_id' => $journal->id,
                    'no_inc' => 1,
                    'coa_id' => $record->coa_id_source,
                    'coa_code' => $coaThirdSource->code,
                    'debet_amount' => $record->payment_loan_total,
                    'credit_amount' => 0,
                    'description' => $coaThirdSource->name,
                ]);

                //Journal from coa destination
                GeneralJournalDetail::create([
                    'jurnal_id' => $journal->id,
                    'no_inc' => 2,
                    'coa_id' => $record->coa_id_destination,
                    'coa_code' => $coaThirdDestination->code,
                    'debet_amount' => 0,
                    'credit_amount' => $record->payment_loan_total,
                    'description' => $coaThirdDestination->name,
                ]);

                // $coaThirdSource->balance = (float) $coaThirdSource->balance + (float) $record->payment_loan_total;
                // $coaThirdSource->save();
                // $coaThirdDestination->balance = (float) $coaThirdDestination->balance - (float) $record->payment_loan_total;
                // $coaThirdDestination->save();

                // update total loan masing2 employee
                foreach ($record->employeePayrollDetails as $value) {
                    $employee = Employee::find($value->employee_id);
                    $outstanding = (float) $employee->total_loan - (float) $value->loan_payment;

                    if ((float) $value->loan_payment > 0) {
                        Receivable::create([
                            'transaction_date' => Carbon::now(),
                            'employee_id' => $value->employee_id,
                            'total_loan' => $employee->total_loan,
                            'payment_amount' => $value->loan_payment,
                            'is_jurnal' => 1,
                            'coa_id_source' => $record->coa_id_loan,
                            'coa_id_destination' => $record->coa_id_source,
                            'description' => "Pembayaran dari payroll",
                            'created_by' => auth()->user()->email,
                            'updated_by' => auth()->user()->email,
                        ]);
                    }

                    $employee->total_loan = (float) $employee->total_loan - (float) $value->loan_payment;
                    $employee->save();
                }
            }

            $record->update([
                'is_jurnal' => 1,
                'updated_by' => auth()->user()->email
            ]);
        });
    }
}
