<?php

namespace App\Filament\Pages;

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
            Grid::make(2)
                ->schema([
                    Select::make('project_plan_id')
                        ->options(ProjectPlan::all()->pluck('name', 'id'))
                        ->preload()
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(function ($state, callable $get, Closure $set) {
                            $period = explode(' - ', $get('period'));
                            if ($period[0] != '' && $state) {
                                $startDate = Carbon::parse(Str::replace('/', '-', $period[0]))->format('Y-m-d');
                                $endDate = Carbon::parse(Str::replace('/', '-', $period[1]))->format('Y-m-d');
                                $this->frameSrc = env('APP_URL') . "/profitloss/pdf/{$state}/{$startDate}/{$endDate}";
                            } else {
                                $this->frameSrc = "";
                            }
                        })
                        ->searchable(),
                    DateRangePicker::make('period')
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(function ($state, callable $get, Closure $set) {
                            if ($get('project_plan_id') && $state) {
                                $period = explode(' - ', $state);
                                $startDate = Carbon::parse(Str::replace('/', '-', $period[0]))->format('Y-m-d');
                                $endDate = Carbon::parse(Str::replace('/', '-', $period[1]))->format('Y-m-d');
                                $this->frameSrc = env('APP_URL') . "/profitloss/pdf/{$get('project_plan_id')}/{$startDate}/{$endDate}";
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
