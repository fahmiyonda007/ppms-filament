<?php

namespace App\Exports;

use App\Models\ProjectPlan;
use App\Models\ProjectPlanDetail;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ProjectPlanExport implements FromView
{
    protected ProjectPlan $record;

    function __construct($record)
    {
        $this->record = $record;
    }

    public function view(): View
    {
        $invoiceDate = Carbon::now()->format('dmYs');
        return view('filament/resources/projectplan', [
            'record' => $this->record,
            'fileName' => "plan_{$this->record->code}_{$invoiceDate}",
            'total' => 0,
        ]);
    }
}
