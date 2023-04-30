<?php

namespace App\Filament\Common;

use App\Models\CashTransfer;
use App\Models\GeneralJournal;
use App\Models\GeneralJournalDetail;
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

    public static function getNewTransactionId(): string
    {
        $journal = CashTransfer::whereDate('created_at', Carbon::today())->max('transaction_id');
        $formatCode = 'CT-' . Carbon::today()->format('Ymd');
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
