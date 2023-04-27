<?php

namespace App\Http\Controllers;

use App\Exports\ProjectPlanExport;
use App\Models\ProjectPlan;
use App\Models\ProjectPlanDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ProjectPlanController extends Controller
{
    public function __invoke(ProjectPlan $record)
    {
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_{$record->code}_{$invoiceDate}.pdf";
        $total = 0;

        $pdf = PDF::loadView('filament/resources/projectplan', compact('record', 'fileName', 'total'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($fileName);
    }

    public function PrintExcel(ProjectPlan $record)
    {
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_{$record->code}_{$invoiceDate}";
        return Excel::download(new ProjectPlanExport($record), "{$fileName}.xlsx");
    }
}
