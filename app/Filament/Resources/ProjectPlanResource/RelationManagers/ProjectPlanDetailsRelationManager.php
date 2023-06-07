<?php

namespace App\Filament\Resources\ProjectPlanResource\RelationManagers;

use App\Filament\Common\Common;
use App\Filament\Resources\Common\JournalRepository;
use App\Filament\Resources\ProjectPlanResource;
use App\Models\ProjectPlanDetailPayment;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Position;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;
use Illuminate\Support\Str;


class ProjectPlanDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectPlanDetails';

    // protected static ?string $recordTitleAttribute = 'project_plan_id';
    protected static ?string $title = 'Details';
    protected static ?string $label = 'Details';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Tabs::make('Detail')
                    ->tabs([
                        Tabs\Tab::make('INFORMATION')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('unit_kavling')
                                            ->required()
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('unit_price')
                                            ->required()
                                            ->numeric()
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set, $state) {
                                                $set('deal_price', $state);
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn (Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                    ]),
                                Grid::make(1)
                                    ->schema([
                                        Forms\Components\TextArea::make('description')
                                            ->maxLength(2000),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('no_shm')
                                            ->label('No. SHM')
                                            ->alphaNum(),
                                        Forms\Components\TextInput::make('no_imb')
                                            ->label('No. IMB')
                                            ->alphaNum(),
                                        Forms\Components\TextInput::make('land_width')
                                            ->label('Land Width (m2)')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('building_width')
                                            ->label('Building Width (m2)')
                                            ->numeric(),

                                    ]),
                            ]),
                        Tabs\Tab::make('PRICE AND MORE')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        // Forms\Components\Select::make('booking_by')
                                        //     ->relationship('customer', 'name')
                                        //     ->searchable()
                                        //     ->preload()
                                        //     ->reactive()
                                        //     ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name} - {$record->phone}")
                                        //     ->label('Booking By'),
                                        // Forms\Components\DatePicker::make('booking_date')
                                        //     ->required(function (callable $get) {
                                        //         return $get('booking_by') != null;
                                        //     }),
                                        // Forms\Components\Select::make('sales_id')
                                        //     ->relationship(
                                        //         'employee',
                                        //         'employee_name',
                                        //         fn(Builder $query) => $query
                                        //             ->where('department', 'SALES')
                                        //             ->Where('is_resign', 0)
                                        //     )
                                        //     ->required(function (callable $get) {
                                        //         return $get('booking_by') != null;
                                        //     })
                                        //     ->searchable()
                                        //     ->preload()
                                        //     ->columnSpanFull()
                                        //     // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->empname} - {$record->phone}")
                                        //     ->label('Sales'),
                                        Forms\Components\TextInput::make('net_price')
                                            ->numeric()
                                            ->disabled()
                                            ->mask(
                                                fn (Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('deal_price')
                                            ->numeric()
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn (Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        // Forms\Components\TextInput::make('down_payment')
                                        //     ->numeric()
                                        //     ->required(function (callable $get) {
                                        //         return $get('booking_by') != null;
                                        //     })
                                        //     ->mask(
                                        //         fn (Mask $mask) => $mask
                                        //             ->numeric()
                                        //             ->decimalPlaces(2)
                                        //             ->decimalSeparator(',')
                                        //             ->thousandsSeparator(',')
                                        //     ),
                                        Forms\Components\Select::make('coa_id_source')
                                            ->label('COA Source')
                                            ->required()
                                            ->reactive()
                                            ->preload()
                                            ->searchable()
                                            ->options(function () {
                                                $datas = Common::getViewCoaMasterDetails([
                                                    ["level_first_id", "=", 1],
                                                    ["balance", ">", 0],
                                                    ["level_second_code", "=", "01"],
                                                ])->get();
                                                return $datas->pluck('level_third_name', 'level_third_id');
                                            }),
                                        Forms\Components\TextInput::make('notary_fee')
                                            ->label('Notary Fee [501003]')
                                            ->numeric()
                                            ->default(0)
                                            ->mask(
                                                fn (Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('tax_rate')
                                            ->label('Tax rate (%)')
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn (Mask $mask) => $mask
                                                    ->numeric()
                                                    ->minValue(0.01)
                                                    ->maxValue(100)
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator('.')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('tax')
                                            ->label('Tax [501004]')
                                            ->numeric()
                                            ->disabled()
                                            ->mask(
                                                fn (Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('commission_rate')
                                            ->label('Commission rate (%)')
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn (Mask $mask) => $mask
                                                    ->numeric()
                                                    ->minValue(0.01)
                                                    ->maxValue(100)
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator('.')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('commission')
                                            ->label('Commission [501006]')
                                            ->numeric()
                                            ->disabled()
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn (Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('other_commission')
                                            ->label('Other Commission [501007]')
                                            ->numeric()
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn (Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('added_bonus')
                                            ->numeric()
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn (Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_jurnal')->label('Post Journal')->boolean(),
                Tables\Columns\TextColumn::make('unit_kavling')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')->money('idr', true),
                Tables\Columns\TextColumn::make('unit_price')->money('idr', true),
                Tables\Columns\TextColumn::make('net_price')->money('idr', true),
                Tables\Columns\TextColumn::make('deal_price')->money('idr', true),
            ])
            ->filters([
                TextFilter::make('unit_kavling')
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        $isProgress = $livewire->ownerRecord->progress < 100.0;
                        return $isEdit && $isProgress;
                    })
                    ->using(function (HasRelationshipTable $livewire, array $data): Model {
                        $data['created_by'] = auth()->user()->email;
                        return $livewire->getRelationship()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->visible(function (RelationManager $livewire, Model $record) {
                            $isEdit = Str::contains($livewire->pageClass, '\Edit');
                            $isProgress = $livewire->ownerRecord->progress < 100.0;
                            $isJournal = $record->is_jurnal == 0;
                            return $isEdit && $isJournal;
                        })
                        ->using(function (Model $record, array $data): Model {
                            $data['updated_by'] = auth()->user()->email;
                            $record->update($data);
                            return $record;
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->visible(function (RelationManager $livewire, Model $record) {
                            $isEdit = Str::contains($livewire->pageClass, '\Edit');
                            $isProgress = $livewire->ownerRecord->progress < 100.0;
                            $isJournal = $record->is_jurnal == 0;
                            return $isEdit && $isJournal;
                        }),
                    Tables\Actions\ViewAction::make(),
                ]),
                Tables\Actions\Action::make('post_jurnal')
                    ->button()
                    ->label('Post Journal')
                    ->icon('heroicon-s-cash')
                    ->action(fn ($action, $record, $livewire) => static::postJournal($action, $record, $livewire->ownerRecord))
                    ->requiresConfirmation()
                    ->visible(function (Model $record) {
                        return $record->is_jurnal == 0;
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make()
                //     ->visible(function (RelationManager $livewire) {
                //         $isEdit = Str::contains($livewire->pageClass, '\Edit');
                //         $isProgress = $livewire->ownerRecord->progress < 100.0;
                //         return $isEdit && $isProgress;
                //     }),
            ]);
    }

    protected function getTableActionsPosition(): ?string
    {
        return Position::BeforeCells;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 20];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->fastPaginate($this->getTableRecordsPerPage());
    }

    protected static function calculatePrice(callable $get, Closure $set): void
    {
        $dealPrice = (float) $get('deal_price');

        $dp = (float) $get('down_payment');

        $notaryFee = (float) $get('notary_fee');
        $oth = (float) $get('other_commission');
        $addBonus = (float) $get('added_bonus');
        $calc = $dealPrice - ($notaryFee + $addBonus + $oth);

        $commissionRate = (float) $get('commission_rate');
        $commission = $calc * $commissionRate / 100;

        $taxRate = (float) $get('tax_rate');
        $tax = $calc * $taxRate / 100;

        $calc = $calc - $tax - $commission;

        $set('tax', (string) $tax);
        $set('commission', (string) $commission);
        $set('net_price', (string) $calc);
        // $set('deal_price', (string)$calc);
    }

    protected static function postJournal($action, $record, $header)
    {
        if (
            $record->notary_fee == null ||
            $record->tax == null ||
            $record->commission == null ||
            $record->other_commission == null
        ) {
            Notification::make()
                ->title('Pastikan Notary Fee, Tax, Commission, Other Commission sudah terisi dengan benar.')
                ->danger()
                ->send();
            $action->halt();
        }

        JournalRepository::ProjectPlanDetailPostJournal($record, $header);
        redirect(ProjectPlanResource::getUrl('edit', ['record' => $header]));
    }
}
