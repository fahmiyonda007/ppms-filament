<?php

namespace App\Http\Controllers;

use App\Models\CoaThird;
use App\Models\ProjectPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function ProfitLossPdf($projectPlanId, $startDate, $endDate)
    {
        $projectPlan = ProjectPlan::find($projectPlanId);
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_{$projectPlan->code}_{$invoiceDate}.pdf";
        $ppName = $projectPlan->name;

        $reportData = [
            'projectName' => $projectPlan->name,
            'startDate' => Carbon::parse($startDate)->format('d M Y'),
            'endDate' => Carbon::parse($endDate)->format('d M Y'),
        ];
        $record = DB::select("CALL SP_ProfitLoss ({$projectPlanId}, '{$startDate}', '{$endDate}')");
        
        $pdf = PDF::loadView('report/ProfitLoss/index', compact('reportData', 'record', 'fileName'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream($fileName);
    }

    public function CashFlowPdf($id, $startDate, $endDate)
    {
        $coa = CoaThird::find($id);
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_{$coa->code}_{$invoiceDate}.pdf";
        $ppName = $coa->name;

        $reportData = [
            'projectName' => $coa->name,
            'startDate' => Carbon::parse($startDate)->format('d M Y'),
            'endDate' => Carbon::parse($endDate)->format('d M Y'),
        ];
        $record = DB::select("CALL SP_CashFlow ({$id}, '{$startDate}', '{$endDate}')");
        
        $pdf = PDF::loadView('report/CashFlow/index', compact('reportData', 'record', 'fileName'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream($fileName);
    }
    
}
