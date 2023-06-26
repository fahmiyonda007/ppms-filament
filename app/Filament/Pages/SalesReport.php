<?php

namespace App\Filament\Pages;

use App\Models\ProjectPlan;
use App\Models\SysLookup;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use illuminate\Support\Str;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;

class SalesReport extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.sales-report';
    protected static ?string $title = 'Sales Report';
    protected static ?string $navigationLabel = 'Sales Report';
    protected static ?string $slug = 'reports/sales-report';
    protected static ?string $navigationGroup = 'Reports';

    public $frameSrc;
    public bool $showRpt = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Select::make('project_plan_id')
                        ->options(function () {
                            $main = ProjectPlan::all()->pluck('name', 'id')->toArray();
                            // $add = SysLookup::where('group_name', 'ADD PROJECT')->get()->pluck('name', 'name')->toArray();
                            //$datas = array_merge($add, $main);

                            //dd($main);
                            return $main;
                        })
                        ->hidden()
                        ->label("Reference Code")
                        ->reactive()
                        ->afterStateUpdated(function (callable $get, $state) {

                            $refCode = $state ?? "xxx";
                            $this->frameSrc = env('APP_URL') . "/salesreport/pdf/{$refCode}";

                            $this->showRpt = false;
                        }),
                ])
        ];
    }
}
