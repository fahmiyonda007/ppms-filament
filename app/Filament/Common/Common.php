<?php

namespace App\Filament\Common;

use App\Models\GeneralJournal;
use App\Models\SysLookup;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Common
{
    public static string $depositToko = 'DEPOSIT TOKO';

    public static function getDepositToko(): string
    {
        $res = SysLookup::where('group_name', 'DEPOSIT')
            ->where('name', static::$depositToko)
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
}
