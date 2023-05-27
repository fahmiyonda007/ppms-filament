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
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use illuminate\Support\Str;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;

class ProfitLoss extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.profit-loss';
    protected static ?string $title = 'Profit Loss';
    protected static ?string $navigationLabel = 'Profit Loss';
    protected static ?string $slug = 'reports/profit-loss';
    protected static ?string $navigationGroup = 'Reports';

    public $frameSrc;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    Select::make('project_plan_id')
                        ->options(function () {
                            $main = ProjectPlan::all()->pluck('name', 'id')->toArray();
                            // $add = SysLookup::where('group_name', 'ADD PROJECT')->get()->pluck('name', 'name')->toArray();
                            //$datas = array_merge($add, $main);

                            //dd($main);
                            return $main;
                        })
                        ->preload()
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(function ($state, callable $get) {
                            if ($get('period_start') && $get('period_end') && $state) {
                                //dd($state);
                                $startDate = Carbon::parse($get('period_start'))->format('Y-m-d');
                                $endDate = Carbon::parse($get('period_end'))->format('Y-m-d');
                                $this->frameSrc = env('APP_URL') . "/profitloss/pdf/{$state}/{$startDate}/{$endDate}";
                            } else {
                                $this->frameSrc = "";
                            }
                        })
                        ->searchable(),
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('period_start')
                                ->reactive()
                                // ->default(Carbon::now()->startOfMonth())
                                ->maxDate(function (callable $get) {
                                    if ($get('period_end')) {
                                        return new Carbon($get('period_end'));
                                    }
                                })
                                ->afterStateUpdated(function (callable $get, $state) {
                                    if ($state && $get('period_end') && $get('project_plan_id')) {
                                        $period = explode(' - ', $state);
                                        $startDate = Carbon::parse($state)->format('Y-m-d');
                                        $endDate = Carbon::parse($get('period_end'))->format('Y-m-d');
                                        $this->frameSrc = env('APP_URL') . "/profitloss/pdf/{$get('project_plan_id')}/{$startDate}/{$endDate}";
                                    } else {
                                        $this->frameSrc = "";
                                    }
                                }),
                            DatePicker::make('period_end')
                                ->reactive()
                                // ->default(Carbon::now()->endOfMonth())
                                ->minDate(function (callable $get) {
                                    if ($get('period_start')) {
                                        return new Carbon($get('period_start'));
                                    }
                                })
                                ->afterStateUpdated(function (callable $get, $state) {
                                    if ($state && $get('period_start') && $get('project_plan_id')) {
                                        $period = explode(' - ', $state);
                                        $startDate = Carbon::parse($get('period_start'))->format('Y-m-d');
                                        $endDate = Carbon::parse($state)->format('Y-m-d');
                                        $this->frameSrc = env('APP_URL') . "/profitloss/pdf/{$get('project_plan_id')}/{$startDate}/{$endDate}";
                                    } else {
                                        $this->frameSrc = "";
                                    }
                                }),
                        ]),
                    // DateRangePicker::make('period')
                    //     ->reactive()
                    //     ->required()
                    //     ->afterStateUpdated(function ($state, callable $get, Closure $set) {

                    //         if ($get('project_plan_id') && $state) {

                    //             $period = explode(' - ', $state);
                    //             $startDate = Carbon::parse(Str::replace('/', '-', $period[0]))->format('Y-m-d');
                    //             $endDate = Carbon::parse(Str::replace('/', '-', $period[1]))->format('Y-m-d');
                    //             $this->frameSrc = env('APP_URL') . "/profitloss/pdf/{$get('project_plan_id')}/{$startDate}/{$endDate}";
                    //         } else {
                    //             $this->frameSrc = "";
                    //         }
                    //     }),

                    // TextInput::make('url'),
                    // ViewField::make('iframe')
                    //     ->label('')
                    //     ->view('forms.components.iframe')
                    //     ->statePath($this->frameSrc)
                    //     ->columnSpanFull(),
                ])
        ];
    }
}
