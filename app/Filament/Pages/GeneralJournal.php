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

class GeneralJournal extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.general-journal';
    protected static ?string $title = 'General Journal';
    protected static ?string $navigationLabel = 'General Journal';
    protected static ?string $slug = 'reports/general-journal';
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
                    TextInput::make('refCode')
                        ->label("Reference Code")
                        ->reactive()
                        ->afterStateUpdated(function (callable $get, $state) {
                            $refCode = $state??"0";

                            $startDate = Carbon::parse($get('period_start')??"1999-01-01")->format('Y-m-d');
                            $endDate = Carbon::parse($get('period_end')??"1999-01-01")->format('Y-m-d');

                            $this->frameSrc = env('APP_URL') . "/generaljournal/pdf/{$refCode}/{$startDate}/{$endDate}";

                            $this->showRpt = false;
                        }),
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

                                    $refCode = $get('refCode') ?? "0";
                                    $startDate = Carbon::parse($state)->format('Y-m-d');
                                    $endDate = Carbon::parse($get('period_end')??"1999-01-01")->format('Y-m-d');
                                    $this->frameSrc = env('APP_URL') . "/generaljournal/pdf/{$refCode}/{$startDate}/{$endDate}";

                                    $this->showRpt = false;
                                }),
                            DatePicker::make('period_end')
                                ->reactive()
                                ->minDate(function (callable $get) {
                                    if ($get('period_start')) {
                                        return new Carbon($get('period_start'));
                                    }
                                })
                                ->afterStateUpdated(function (callable $get, $state) {

                                    $refCode = $get('refCode') ?? "0";
                                    $startDate = Carbon::parse($get('period_start')??"1999-01-01")->format('Y-m-d');
                                    $endDate = Carbon::parse($state)->format('Y-m-d');
                                    $this->frameSrc = env('APP_URL') . "/generaljournal/pdf/{$refCode}/{$startDate}/{$endDate}";

                                    $this->showRpt = false;
                                }),
                        ]),
                ])
        ];
    }
}
