<?php

namespace App\Filament\Common;

use App\Models\CashFlow;
use App\Models\CashTransfer;
use App\Models\DepositVendor;
use App\Models\EmployeeLoan;
use App\Models\EmployeePayroll;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
use App\Models\ProjectPayment;
use App\Models\Receivable;
use App\Models\SysLookup;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Common
{
    public static string $depositToko = '102';

    public static function getDepositToko(): string
    {
        $res = SysLookup::where('group_name', 'DEPOSIT')
            ->where('code', static::$depositToko)
            ->get();

        return $res->name;
    }

    public static function getNewJournalId(): string
    {
        $journal = GeneralJournal::whereDate('created_at', Carbon::today())->max('jurnal_id');
        $formatCode = 'GJ-' . Carbon::today()->format('Ymd');
        $lastCode = $journal ?? $formatCode . '000';
        $num = (int)Str::substr($lastCode, Str::length($lastCode) - 3, 3) + 1;
        $len = str_pad($num, 3, '0', STR_PAD_LEFT);
        return $formatCode . $len;
    }

    public static function getNewCashTransferTransactionId(): string
    {
        $journal = CashTransfer::whereDate('created_at', Carbon::today())->max('transaction_id');
        $formatCode = 'CT-' . Carbon::today()->format('Ymd');
        $lastCode = $journal ?? $formatCode . '000';
        $num = (int)Str::substr($lastCode, Str::length($lastCode) - 3, 3) + 1;
        $len = str_pad($num, 3, '0', STR_PAD_LEFT);
        return $formatCode . $len;
    }

    public static function getNewDepositVendorTransactionId(): string
    {
        $journal = DepositVendor::whereDate('created_at', Carbon::today())->max('transaction_code');
        $formatCode = 'DPST-' . Carbon::today()->format('Ymd');
        $lastCode = $journal ?? $formatCode . '000';
        $num = (int)Str::substr($lastCode, Str::length($lastCode) - 3, 3) + 1;
        $len = str_pad($num, 3, '0', STR_PAD_LEFT);
        return $formatCode . $len;
    }

    public static function getNewCashFlowTransactionId(): string
    {
        $journal = CashFlow::whereDate('created_at', Carbon::today())->max('transaction_code');
        $formatCode = 'CF-' . Carbon::today()->format('Ymd');
        $lastCode = $journal ?? $formatCode . '000';
        $num = (int)Str::substr($lastCode, Str::length($lastCode) - 3, 3) + 1;
        $len = str_pad($num, 3, '0', STR_PAD_LEFT);
        return $formatCode . $len;
    }

    public static function getNewReceivableTransactionId(): string
    {
        $journal = Receivable::whereDate('created_at', Carbon::today())->max('transaction_code');
        $formatCode = 'RC-' . Carbon::today()->format('Ymd');
        $lastCode = $journal ?? $formatCode . '000';
        $num = (int)Str::substr($lastCode, Str::length($lastCode) - 3, 3) + 1;
        $len = str_pad($num, 3, '0', STR_PAD_LEFT);
        return $formatCode . $len;
    }

    public static function getNewProjectPaymentTransactionId(): string
    {
        $journal = ProjectPayment::whereDate('created_at', Carbon::today())->max('transaction_code');
        $formatCode = 'PAY-' . Carbon::today()->format('Ymd');
        $lastCode = $journal ?? $formatCode . '000';
        $num = (int)Str::substr($lastCode, Str::length($lastCode) - 3, 3) + 1;
        $len = str_pad($num, 3, '0', STR_PAD_LEFT);
        return $formatCode . $len;
    }

    public static function getNewEmployeeLoanTransactionId(): string
    {
        $journal = EmployeeLoan::whereDate('created_at', Carbon::today())->max('transaction_code');
        $formatCode = 'EL-' . Carbon::today()->format('Ymd');
        $lastCode = $journal ?? $formatCode . '000';
        $num = (int)Str::substr($lastCode, Str::length($lastCode) - 3, 3) + 1;
        $len = str_pad($num, 3, '0', STR_PAD_LEFT);
        return $formatCode . $len;
    }

    public static function getNewEmployeePayrollTransactionId(): string
    {
        $journal = EmployeePayroll::whereDate('created_at', Carbon::today())->max('transaction_code');
        $formatCode = 'EP-' . Carbon::today()->format('Ymd');
        $lastCode = $journal ?? $formatCode . '000';
        $num = (int)Str::substr($lastCode, Str::length($lastCode) - 3, 3) + 1;
        $len = str_pad($num, 3, '0', STR_PAD_LEFT);
        return $formatCode . $len;
    }

    public static function getViewCoaMasterDetails(?array $condition): Builder
    {
        $datas = DB::table('v_coa_master_details')
            ->where($condition);

        return $datas;
    }

}
