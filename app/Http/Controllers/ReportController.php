<?php

namespace App\Http\Controllers;

use App\Models\CoaThird;
use App\Models\ProjectPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function ProfitLossPdf($projectPlanId, $startDate, $endDate)
    {
        $projectPlan = ProjectPlan::find($projectPlanId);
        $invoiceDate = Carbon::now()->format('dmYs');
        $code = $projectPlan->id ?? $projectPlanId;
        $name = $projectPlan->name ?? $projectPlanId;
        $fileName = "plan_{$code}_{$invoiceDate}.pdf";
        $ppName = $name;

        $reportData = [
            'projectName' => $ppName,
            'startDate' => Carbon::parse($startDate)->format('d M Y'),
            'endDate' => Carbon::parse($endDate)->format('d M Y'),
        ];
        $record = DB::select("CALL SP_ProfitLoss ('{$code}', '{$startDate}', '{$endDate}')");

        $pdf = PDF::loadView('report/ProfitLoss/index', compact('reportData', 'record', 'fileName'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream($fileName);
    }

    private function generateDateRange(Carbon $start_date, Carbon $end_date)
    {
        $dates = [];

        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    public function CashFlowPdf($startDate, $endDate)
    {
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_CashFlowDetail_{$invoiceDate}.pdf";

        //$period = CarbonPeriod::create($startDate, $endDate);

        // Iterate over the period
        //foreach ($period as $date) {
        //    echo $date->format('Y-m-d');
        //}

        // Convert the period to an array of dates
        $dates = $this->generateDateRange(Carbon::parse($startDate), Carbon::parse($endDate));

        $reportData = [
            'dateArray' => $dates,
            'startDate' => Carbon::parse($startDate)->format('d M Y'),
            'endDate' => Carbon::parse($endDate)->format('d M Y'),
        ];
        //dd($data);
        $record = Collect(DB::select("CALL SP_CashFlow ('{$startDate}', '{$endDate}')"))->groupBy('name');

        $pdf = PDF::loadView('report/CashFlow/index', compact('reportData', 'record', 'fileName'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream($fileName);
    }

    public function DailyCostReportPdf($periodDate)
    {
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_daily_cost_report_{$invoiceDate}.pdf";


        $reportData = [
            'periodDate' => Carbon::parse($periodDate)->format('d M Y'),
        ];
        $record = DB::select("CALL SP_DailyCostReport ('{$periodDate}')");

        $pdf = PDF::loadView('report/DailyCostReport/index', compact('reportData', 'record', 'fileName'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream($fileName);
    }

    public function CashFlowLevel2Pdf($startDate, $endDate)
    {
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_CashFlowSummary_{$invoiceDate}.pdf";

        //$period = CarbonPeriod::create($startDate, $endDate);

        // Iterate over the period
        //foreach ($period as $date) {
        //    echo $date->format('Y-m-d');
        //}

        // Convert the period to an array of dates
        $dates = $this->generateDateRange(Carbon::parse($startDate), Carbon::parse($endDate));

        $reportData = [
            'dateArray' => $dates,
            'startDate' => Carbon::parse($startDate)->format('d M Y'),
            'endDate' => Carbon::parse($endDate)->format('d M Y'),
        ];
        //dd($data);
        $record = Collect(DB::select("CALL SP_CashFlow_Level2 ('{$startDate}', '{$endDate}')"))->groupBy('name');

        $pdf = PDF::loadView('report/CashFlowLevel2/index', compact('reportData', 'record', 'fileName'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream($fileName);
    }

    public function ReportSummarySalaryPdf($startDate, $endDate)
    {
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_ReportSummarySalary_{$invoiceDate}.pdf";

        $dates = $this->generateDateRange(Carbon::parse($startDate), Carbon::parse($endDate));

        $reportData = [
            'dateArray' => $dates,
            'startDate' => Carbon::parse($startDate)->format('d M Y'),
            'endDate' => Carbon::parse($endDate)->format('d M Y'),
        ];
        $record = DB::select("CALL SP_ReportSummarySalary ( '{$startDate}', '{$endDate}')");

        $pdf = PDF::loadView('report/ReportSummarySalary/index', compact('reportData', 'record', 'fileName'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream($fileName);
    }

    public function VendorLiabilitiesPdf($status, $startDate, $endDate)
    {

        //dd($status);
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_VendorLiabilities_{$invoiceDate}.pdf";
        $reportData = [
            'startDate' => Carbon::parse($startDate)->format('d M Y'),
            'endDate' => Carbon::parse($endDate)->format('d M Y'),
        ];
        $record = DB::select("CALL SP_VendorLiabilities ('{$status}', '{$startDate}', '{$endDate}')");

        $pdf = PDF::loadView('report/VendorLiabilities/index', compact('reportData', 'record', 'fileName'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream($fileName);
    }

    public function GeneralJournalPdf($refCode, ?string $startDate, ?string $endDate)
    {

        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_GeneralJournal_{$invoiceDate}.pdf";

        $reportData = [
            'startDate' => Carbon::parse($startDate)->format('d M Y'),
            'endDate' => Carbon::parse($endDate)->format('d M Y'),
        ];
        $record = DB::select("CALL SP_GeneralJournal ('{$refCode}', '{$startDate}', '{$endDate}')");

        $pdf = PDF::loadView('report/GeneralJournal/index', compact('reportData', 'record', 'fileName'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream($fileName);
    }
    public function JournalVoucherPdf($refCode)
    {
        $refCode = Str::replace("'", "", $refCode);
        $invoiceDate = Carbon::now()->format('dmYs');
        $fileName = "plan_JournalVoucher_{$refCode}.pdf";

        $reportData = [

        ];
        $record = DB::select("CALL SP_GeneralJournal ('{$refCode}', '1999-01-01', '1999-01-01')");

        $pdf = PDF::loadView('report/JournalVoucher/index', compact('reportData', 'record', 'fileName'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream($fileName);
    }
}
