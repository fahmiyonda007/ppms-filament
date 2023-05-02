<?php

namespace App\Filament\Resources;

use App\Filament\Common\Common;
use App\Filament\Resources\EmployeePayrollResource\Pages;
use App\Filament\Resources\EmployeePayrollResource\RelationManagers;
use App\Filament\Resources\EmployeePayrollResource\RelationManagers\EmployeePayrollDetailsRelationManager;
use App\Models\CoaThird;
use App\Models\EmployeePayroll;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class EmployeePayrollResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = EmployeePayroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $slug = 'cash/employee-payrolls';
    protected static ?string $navigationGroup = 'Cash';
    protected static ?string $navigationLabel = 'Payrolls';
    // protected static ?string $recordTitleAttribute = 'transaction_code';
    // protected static ?int $navigationSort = 2;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('transaction_code')
                                ->maxLength(20)
                                ->required()
                                ->disabled()
                                ->columnSpanFull()
                                ->default(fn () => Common::getNewEmployeePayrollTransactionId()),
                            Forms\Components\DatePicker::make('transaction_date')
                                ->required(),
                            Forms\Components\Select::make('project_plan_id')
                                ->relationship('projectPlan', 'name')
                                ->preload()
                                ->searchable(),

                        ]),
                    Grid::make(3)
                        ->schema([
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
                            Forms\Components\Select::make('coa_id_destination')
                                ->label('COA Destination')
                                ->required()
                                ->preload()
                                ->reactive()
                                ->searchable()
                                ->options(function (callable $get) {
                                    $datas = new Collection();
                                    if ($get('coa_id_source') != null) {
                                        $datas = Common::getViewCoaMasterDetails([
                                            ["level_first_id", "=", 5],
                                            // ["level_second_code", "=", "03"],
                                            // ['level_third_id', '!=', $get('coa_id_source')],

                                        ])->get();
                                    }
                                    return $datas->pluck('level_third_name', 'level_third_id');
                                }),
                            Forms\Components\Select::make('coa_id_loan')
                                ->label('COA Loan')
                                ->required()
                                ->preload()
                                ->reactive()
                                ->searchable()
                                ->options(function (callable $get) {
                                    $datas = new Collection();
                                    if ($get('coa_id_source') != null) {
                                        $datas = Common::getViewCoaMasterDetails([
                                            ["level_first_id", "=", 1],
                                            ["level_second_code", "=", "03"],
                                        ])->get();
                                    }
                                    return $datas->pluck('level_third_name', 'level_third_id');
                                }),
                        ]),
                    Card::make([
                        Placeholder::make('source_start_balance')
                            ->label('')
                            ->content(function (callable $get) {
                                $num = CoaThird::find($get('coa_id_source'))->balance ?? 0;
                                return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                            }),
                        Placeholder::make('destination_start_balance')
                            ->label('')
                            ->content(function (callable $get) {
                                $num = CoaThird::find($get('coa_id_destination'))->balance ?? 0;
                                return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                            }),
                        Placeholder::make('loan_start_balance')
                            ->label('')
                            ->content(function (callable $get) {
                                $num = CoaThird::find($get('coa_id_loan'))->balance ?? 0;
                                return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                            }),

                    ])->columns(3)
                        ->hidden(function ($record) {
                            if ($record) {
                                if ($record->is_jurnal == 1) {
                                    return true;
                                }
                                return false;
                            }
                        }),
                    Card::make([
                        Placeholder::make('source_end_balance')
                            ->label('Source End Balance')
                            ->content(function (callable $get) {
                                $coaThird = CoaThird::find($get('coa_id_source'))->balance ?? 0;
                                $num = (float)$coaThird - (float)$get('amount');
                                return 'Rp ' . number_format($num, 0, ',', '.');
                            }),
                        Placeholder::make('destination_end_balance')
                            ->label('Destination End Balance')
                            ->content(function (callable $get) {
                                $coaThird = CoaThird::find($get('coa_id_destination'))->balance ?? 0;
                                $num = (float)$coaThird + (float)$get('amount');
                                return 'Rp ' . number_format($num, 0, ',', '.');
                            }),
                        AlertBox::make()
                            ->label(label: 'Oops...')
                            ->helperText(text: 'Source End Balance kurang dari 0.')
                            ->resolveIconUsing(name: 'heroicon-o-x-circle')
                            ->warning()
                            ->hidden(function (callable $get) {
                                $coaThird = CoaThird::find($get('coa_id_source'))->balance ?? 0;
                                $num = (float)$coaThird - (float)$get('amount');
                                return $num >= 0;
                            })
                            ->columnSpanFull(),
                    ])->columns(2)
                        ->visible(function ($record) {
                            if ($record) {
                                if ($record->is_jurnal == 1) {
                                    return true;
                                }
                                return false;
                            }
                        }),
                    Card::make([
                        Placeholder::make('payroll_total')
                            ->content(function ($record) {
                                $num = $record->payroll_total ?? 0;
                                return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                            }),
                        Placeholder::make('payment_loan_total')
                            ->content(function ($record) {
                                $num = $record->payment_loan_total ?? 0;
                                return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                            }),

                    ])->columns(2),
                    Forms\Components\TextArea::make('description')
                        ->maxLength(500),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BooleanColumn::make('is_jurnal')->label('Post Journal'),
                Tables\Columns\TextColumn::make('projectPlan.name')
                    ->sortable(['name'])
                    ->searchable(['name']),
                Tables\Columns\TextColumn::make('transaction_code')
                    ->label('Transaction ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date(),
                Tables\Columns\TextColumn::make('payroll_total')->money('idr', true),
                Tables\Columns\TextColumn::make('payment_loan_total')->money('idr', true),
                Tables\Columns\TextColumn::make('coaThirdSource.fullname')
                    ->label('COA Source')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: true),
                Tables\Columns\TextColumn::make('source_start_balance')->money('idr', true),
                Tables\Columns\TextColumn::make('source_end_balance')->money('idr', true),
                Tables\Columns\TextColumn::make('coaThirdDestination.fullname')
                    ->label('COA Destination')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: true),

                Tables\Columns\TextColumn::make('destination_start_balance')->money('idr', true),
                Tables\Columns\TextColumn::make('destination_end_balance')->money('idr', true),
                Tables\Columns\TextColumn::make('coaThirdLoan.fullname')
                    ->label('COA Loan')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_by')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
            ])
            ->filters([
                TextFilter::make('transaction_code'),
                DateFilter::make('transaction_date'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(function ($record) {
                        if ($record) {
                            if ($record->is_jurnal == 1) {
                                return false;
                            }
                            return true;
                        }
                    }),

                Tables\Actions\DeleteAction::make()
                    ->visible(function ($record) {
                        if ($record) {
                            if ($record->is_jurnal == 1) {
                                return false;
                            }
                            return true;
                        }
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make()
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EmployeePayrollDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeePayrolls::route('/'),
            'create' => Pages\CreateEmployeePayroll::route('/create'),
            'view' => Pages\ViewEmployeePayroll::route('/{record}'),
            'edit' => Pages\EditEmployeePayroll::route('/{record}/edit'),
        ];
    }
}
