<?php

namespace App\Filament\Pages;

use App\Filament\Common\Common;
use App\Models\ProjectPlan;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use illuminate\Support\Str;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;

class CashFlow extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.cash-flow';
    protected static ?string $title = 'Cash Flow';
    protected static ?string $navigationLabel = 'Cash Flow';
    protected static ?string $slug = 'reports/cash-flow';
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
                    Select::make('coa_id_source')
                        ->label('Cash Flow Source')
                        ->required()
                        ->reactive()
                        ->preload()
                        ->searchable()
                        ->options(function () {
                            $datas = Common::getViewCoaMasterDetails([
                                ["level_first_id", "=", 1],
                                ["level_second_code", "=", "01"],
                            ])->get();
                            return $datas->pluck('level_third_name', 'level_third_id');
                        })

                        ->afterStateUpdated(function ($state, callable $get, Closure $set) {
                            $period = explode(' - ', $get('period'));
                            if ($period[0] != '' && $state) {
                                $startDate = Carbon::parse(Str::replace('/', '-', $period[0]))->format('Y-m-d');
                                $endDate = Carbon::parse(Str::replace('/', '-', $period[1]))->format('Y-m-d');
                                $this->frameSrc = env('APP_URL') . "/cashflow/pdf/{$state}/{$startDate}/{$endDate}";
                            } else {
                                $this->frameSrc = "";
                            }
                        }),
                    DateRangePicker::make('period')
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(function ($state, callable $get, Closure $set) {
                            if ($get('coa_id_source') && $state) {
                                $period = explode(' - ', $state);
                                $startDate = Carbon::parse(Str::replace('/', '-', $period[0]))->format('Y-m-d');
                                $endDate = Carbon::parse(Str::replace('/', '-', $period[1]))->format('Y-m-d');
                                $this->frameSrc = env('APP_URL') . "/cashflow/pdf/{$get('coa_id_source')}/{$startDate}/{$endDate}";
                            } else {
                                $this->frameSrc = "";
                            }
                        }),

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
