<?php

use App\Http\Controllers\ProjectPlanController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return redirect('admin');
// });
Route::redirect('/', '/admin');
Route::get("/profile", function () {
    // Only verified users may access this route...
})->middleware('verified');

Route::get('/print/{record}', ProjectPlanController::class)->name('plan-pdf')->middleware('verified');
Route::get('projectplan/export-excel/{record}', [ProjectPlanController::class, 'PrintExcel'])->name('plan-excel')->middleware('verified');
Route::get('profitloss/pdf/{projectPlanId}/{startDate}/{endDate}', [ReportController::class, 'ProfitLossPdf'])->name('ProfitLossPdf')->middleware('verified');
Route::get('cashflow/pdf/{startDate}/{endDate}', [ReportController::class, 'CashFLowPdf'])->name('CashFLowPdf')->middleware('verified');
Route::get('cashflowlevel2/pdf/{startDate}/{endDate}', [ReportController::class, 'CashFLowLevel2Pdf'])->name('CashFLowLevel2Pdf')->middleware('verified');
Route::get('dailycostreport/pdf/{periodDate}', [ReportController::class, 'DailyCostReportPdf'])->name('DailyCostReportPdf')->middleware('verified');
Route::get('reportsummarysalary/pdf/{startDate}/{endDate}', [ReportController::class, 'ReportSummarySalaryPdf'])->name('ReportSummarySalaryPdf')->middleware('verified');
Route::get('vendorliabilities/pdf/{status}/{startDate}/{endDate}', [ReportController::class, 'VendorLiabilitiesPdf'])->name('VendorLiabilitiesPdf')->middleware('verified');
Route::get('generaljournal/pdf/{refCode}/{startDate}/{endDate}', [ReportController::class, 'GeneralJournalPdf'])->name('GeneralJournalPdf')->middleware('verified');
Route::get('journalvoucher/pdf/{refCode}', [ReportController::class, 'JournalVoucherPdf'])->name('JournalVoucherPdf')->middleware('verified');
Route::get('salesreport/pdf/{refCode}', [ReportController::class, 'SalesReportPdf'])->name('SalesReportPdf')->middleware('verified');
Route::get('profitlossmonthly/pdf/{refCode}', [ReportController::class, 'ProfitLossMonthlyPdf'])->name('ProfitLossMonthlyPdf')->middleware('verified');
