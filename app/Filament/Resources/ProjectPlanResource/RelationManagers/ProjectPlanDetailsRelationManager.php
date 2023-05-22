<?php

namespace App\Filament\Resources\ProjectPlanResource\RelationManagers;

use App\Models\ProjectPlanDetailPayment;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Contracts\HasRelationshipTable;
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
                                                fn(Mask $mask) => $mask
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
                                            ->alphaNum()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            }),
                                        Forms\Components\TextInput::make('no_imb')
                                            ->label('No. IMB')
                                            ->alphaNum()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            }),
                                        Forms\Components\TextInput::make('land_width')
                                            ->label('Land Width (m2)')
                                            ->numeric()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            }),
                                        Forms\Components\TextInput::make('building_width')
                                            ->label('Building Width (m2)')
                                            ->numeric()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            }),

                                    ]),
                            ]),
                        Tabs\Tab::make('PRICE AND MORE')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('booking_by')
                                            ->relationship('customer', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->reactive()
                                            ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name} - {$record->phone}")
                                            ->label('Booking By'),
                                        Forms\Components\DatePicker::make('booking_date')
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            }),
                                        Forms\Components\Select::make('sales_id')
                                            ->relationship(
                                                'employee',
                                                'employee_name',
                                                fn(Builder $query) => $query
                                                    ->where('department', 'SALES')
                                                    ->Where('is_resign', 0)
                                            )
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->columnSpanFull()
                                            // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->empname} - {$record->phone}")
                                            ->label('Sales'),
                                        Forms\Components\TextInput::make('net_price')
                                            ->numeric()
                                            ->disabled()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->mask(
                                                fn(Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('deal_price')
                                            ->numeric()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn(Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('down_payment')
                                            ->numeric()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->mask(
                                                fn(Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('notary_fee')
                                            ->numeric()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->default(0)
                                            ->mask(
                                                fn(Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('tax_rate')
                                            ->label('Tax rate (%)')
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn(Mask $mask) => $mask
                                                    ->numeric()
                                                    ->minValue(0.01)
                                                    ->maxValue(100)
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator('.')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('tax')
                                            ->numeric()
                                            ->disabled()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->mask(
                                                fn(Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('commission_rate')
                                            ->label('Commission rate (%)')
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn(Mask $mask) => $mask
                                                    ->numeric()
                                                    ->minValue(0.01)
                                                    ->maxValue(100)
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator('.')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('commission')
                                            ->numeric()
                                            ->disabled()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn(Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('other_commission')
                                            ->numeric()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn(Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                        Forms\Components\TextInput::make('added_bonus')
                                            ->numeric()
                                            ->required(function (callable $get) {
                                                return $get('booking_by') != null;
                                            })
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, Closure $set) {
                                                static::calculatePrice($get, $set);
                                            })
                                            ->mask(
                                                fn(Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(2)
                                                    ->decimalSeparator(',')
                                                    ->thousandsSeparator(',')
                                            ),
                                    ]),
                            ]),
                        Tabs\Tab::make('PAYMENT')
                            ->schema([
                                Forms\Components\Select::make('payment_type')
                                    ->required(function (callable $get) {
                                        return $get('booking_by') != null;
                                    })
                                    ->searchable()
                                    ->reactive()
                                    ->options([
                                        'TUNAI' => 'TUNAI',
                                        'TUNAI BERTAHAP' => 'TUNAI BERTAHAP',
                                        'KPR' => 'KPR',
                                    ]),
                                Forms\Components\Select::make('kpr_type')
                                    ->label('KPR Type')
                                    ->required(function (callable $get) {
                                        return $get('booking_by') != null;
                                    })
                                    ->reactive()
                                    ->searchable()
                                    ->required(function (callable $get) {
                                        return $get('payment_type') == 'KPR';
                                    })
                                    ->visible(function (callable $get) {
                                        return $get('payment_type') == 'KPR';
                                    })
                                    ->options([
                                        'PKS' => 'PKS',
                                        'NON PKS' => 'NON PKS',
                                    ]),
                                // Forms\Components\TextInput::make('projectPlanDetailPayments.amount')
                                //     ->numeric()
                                //     ->required(function (callable $get) {
                                //         return $get('payment_type') == 'TUNAI' || $get('kpr_type') == 'NON PKS';
                                //     })
                                //     ->visible(function (callable $get) {
                                //         return $get('payment_type') == 'TUNAI' || $get('kpr_type') == 'NON PKS';
                                //     })
                                //     ->mask(
                                //         fn (Mask $mask) => $mask
                                //             ->numeric()
                                //             ->decimalPlaces(2)
                                //             ->decimalSeparator(',')
                                //             ->thousandsSeparator(',')
                                //     ),
                                Repeater::make('projectPlanDetailPayments')
                                    ->label('Amount Details')
                                    ->relationship()
                                    ->schema([
                                        grid::make(2)->schema([
                                            Forms\Components\DatePicker::make('transaction_date')
                                                ->label("Transaction Date")
                                                ->required(),
                                            Forms\Components\TextInput::make('amount')
                                                ->numeric()
                                                ->required()
                                                ->mask(
                                                    fn(Mask $mask) => $mask
                                                        ->numeric()
                                                        ->minValue(1)
                                                        ->decimalPlaces(2)
                                                        ->decimalSeparator(',')
                                                        ->thousandsSeparator(',')
                                                ),

                                        ])
                                    ])
                                    //->required()
                                    ->required(function (callable $get) {
                                        return $get('payment_type') != NULL;
                                    })
                                    ->visible(function (callable $get) {
                                        return $get('payment_type') != NULL;
                                    })
                                    ->maxItems(function (callable $get) {
                                        $res = 0;
                                        if ($get('payment_type') == 'TUNAI' || $get('kpr_type') == 'NON PKS') {
                                            $res = 1;
                                        } else if ($get('payment_type') == 'TUNAI BERTAHAP' || $get('kpr_type') == 'PKS') {
                                            $res = 1000;
                                        }
                                        return $res;
                                    })
                                    ->disableItemMovement()
                                    ->collapsible()
                                    ->cloneable()
                                    ->columnSpanFull()
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit_kavling')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')->money('idr', true),
                Tables\Columns\TextColumn::make('customer.name')->label('Booking By'),
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
                Tables\Actions\EditAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        $isProgress = $livewire->ownerRecord->progress < 100.0;
                        return $isEdit && $isProgress;
                    })
                    ->using(function (Model $record, array $data): Model {
                        $data['updated_by'] = auth()->user()->email;
                        if ($data['payment_type'] != 'KPR') {
                            $data['kpr_type'] = null;
                        }
                        if ($data['payment_type'] == NULL) {
                            $payment = ProjectPlanDetailPayment::where('plan_detail_id', $record->id);
                            $payment->delete();
                        }
                        $record->update($data);
                        return $record;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        $isProgress = $livewire->ownerRecord->progress < 100.0;
                        return $isEdit && $isProgress;
                    }),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        $isProgress = $livewire->ownerRecord->progress < 100.0;
                        return $isEdit && $isProgress;
                    }),
            ]);
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 20];
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
}