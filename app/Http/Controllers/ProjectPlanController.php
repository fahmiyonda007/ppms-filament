<?php

namespace App\Http\Controllers;

use App\Exports\ProjectPlanExport;
use App\Models\ProjectPlan;
use App\Models\ProjectPlanDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProjectPlanController extends Controller
{
    public function __invoke(ProjectPlan $record)
    {
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_{$record->code}_{$invoiceDate}.pdf";
        $total = 0;

        $record = DB::table('v_general_journal_details')->where('project_plan_id', $record->id)->get();

        $pdf = PDF::loadView('filament/resources/projectplan', compact('record', 'fileName', 'total'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($fileName);
    }

    public function PrintExcel(ProjectPlan $record)
    {
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_{$record->code}_{$invoiceDate}";
        $data = DB::table('v_general_journal_details')->where('project_plan_id', $record->id)->get();
        return Excel::download(new ProjectPlanExport($data), "{$fileName}.xlsx");
    }
}
