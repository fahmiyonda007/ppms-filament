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
    protected static ?string $title = 'Daily Cost Report';
    protected static ?string $navigationLabel = 'Daily Cost Report';
    protected static ?string $slug = 'reports/daily-cost-report';
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
                    DatePicker::make('period')
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(function ($state, callable $get, Closure $set) {
                            if ($state) {
                                $period = $state;                              
                                $periodDate = Carbon::parse(Str::replace('/', '-', $period))->format('Y-m-d');
                                
                                $this->frameSrc = env('APP_URL') . "/dailycostreport/pdf/{$periodDate}";
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
