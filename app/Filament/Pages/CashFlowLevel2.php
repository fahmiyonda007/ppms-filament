<?php

namespace App\Filament\Pages;

use App\Filament\Common\Common;
use App\Models\ProjectPlan;
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
use Savannabits\Flatpickr\Flatpickr;

class CashFlowLevel2 extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.cash-flow-level2';
    protected static ?string $title = 'Cash Flow Summary';
    protected static ?string $navigationLabel = 'Cash Flow Summary';
    protected static ?string $slug = 'reports/cash-flow-level2';
    protected static ?string $navigationGroup = 'Reports';

    public $frameSrc;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    // Flatpickr::make('read_at')->rangePicker(),
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
                                    if ($state && $get('period_end')) {
                                        $period = explode(' - ', $state);
                                        $startDate = Carbon::parse($state)->format('Y-m-d');
                                        $endDate = Carbon::parse($get('period_end'))->format('Y-m-d');
                                        $this->frameSrc = env('APP_URL') . "/cashflowlevel2/pdf/{$startDate}/{$endDate}";
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
                                    if ($state && $get('period_start')) {
                                        $period = explode(' - ', $state);
                                        $startDate = Carbon::parse($get('period_start'))->format('Y-m-d');
                                        $endDate = Carbon::parse($state)->format('Y-m-d');
                                        $this->frameSrc = env('APP_URL') . "/cashflowlevel2/pdf/{$startDate}/{$endDate}";
                                    } else {
                                        $this->frameSrc = "";
                                    }
                                }),
                        ]),
                    // DateRangePicker::make('period')
                    //     ->reactive()
                    //     ->required()
                    //     ->afterStateUpdated(function ($state) {
                    //         if ($state) {
                    //             $period = explode(' - ', $state);
                    //             $startDate = Carbon::parse(Str::replace('/', '-', $period[0]))->format('Y-m-d');
                    //             $endDate = Carbon::parse(Str::replace('/', '-', $period[1]))->format('Y-m-d');
                    //             $this->frameSrc = env('APP_URL') . "/cashflow/pdf/{$startDate}/{$endDate}";
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
