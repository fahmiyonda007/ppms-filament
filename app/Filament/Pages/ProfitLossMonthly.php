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

class ProfitLossMonthly extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.profit-loss-monthly';
    protected static ?string $title = 'Profit Loss Monthly';
    protected static ?string $navigationLabel = 'Profit Loss Monthly';
    protected static ?string $slug = 'reports/profit-loss-monthly';
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
                        ->label("Year Period")
                        ->reactive()
                        ->afterStateUpdated(function (callable $get, $state) {

                            $refCode = $state ?? "xxx";
                            $this->frameSrc = env('APP_URL') . "/profitlossmonthly/pdf/{$refCode}";

                            $this->showRpt = false;
                        }),
                ])
        ];
    }
}
