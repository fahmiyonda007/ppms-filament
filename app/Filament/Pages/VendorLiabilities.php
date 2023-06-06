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

class VendorLiabilities extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.vendor-liabilities';
    protected static ?string $title = 'Vendor Liabilities';
    protected static ?string $navigationLabel = 'Vendor Liabilities';
    protected static ?string $slug = 'reports/vendor-liabilities';
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
                    Select::make('status')
                        ->options([
                            '9' => 'ALL',
                            '1' => 'DONE',
                            '2' => 'NOT DONE',
                        ])
                        ->preload()
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(function ($state, callable $get) {
                            if ($get('period_start') && $get('period_end') && $state) {

                                $startDate = Carbon::parse($get('period_start'))->format('Y-m-d');
                                $endDate = Carbon::parse($get('period_end'))->format('Y-m-d');
                                $this->frameSrc = env('APP_URL') . "/vendorliabilities/pdf/{$state}/{$startDate}/{$endDate}";
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
                                    if ($state && $get('period_end') && $get('status')) {
                                        $period = explode(' - ', $state);
                                        $startDate = Carbon::parse($state)->format('Y-m-d');
                                        $endDate = Carbon::parse($get('period_end'))->format('Y-m-d');
                                        $this->frameSrc = env('APP_URL') . "/vendorliabilities/pdf/{$get('status')}/{$startDate}/{$endDate}";
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
                                    if ($state && $get('period_start') && $get('status')) {
                                        $period = explode(' - ', $state);
                                        $startDate = Carbon::parse($get('period_start'))->format('Y-m-d');
                                        $endDate = Carbon::parse($state)->format('Y-m-d');
                                        $this->frameSrc = env('APP_URL') . "/vendorliabilities/pdf/{$get('status')}/{$startDate}/{$endDate}";
                                    } else {
                                        $this->frameSrc = "";
                                    }
                                }),
                        ]),

                ])
        ];
    }
}
