<?php

namespace App\Filament\Resources;

use App\Filament\Common\Common;
use App\Filament\Resources\ReceivableResource\Pages;
use App\Filament\Resources\ReceivableResource\RelationManagers;
use App\Models\CoaThird;
use App\Models\Employee;
use App\Models\Receivable;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Closure;
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

class ReceivableResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Receivable::class;
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?string $slug = 'cash/receivables';
    protected static ?string $navigationGroup = 'Cash';
    protected static ?string $navigationLabel = 'Receivables';
    // protected static ?string $recordTitleAttribute = 'transaction_code';
    // protected static ?int $navigationSort = 2;

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
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
                                ->default(fn () => Common::getNewReceivableTransactionId()),
                            Forms\Components\DatePicker::make('transaction_date')
                                ->required()
                                ->default(Carbon::now()),
                            Forms\Components\Select::make('employee_id')
                                ->relationship('employee', 'employee_name', function (Builder $query) {
                                    return $query->where('total_loan', '>', 0);
                                })
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, callable $get, $state) {
                                    $employee = Employee::find($state);
                                    if ($employee) {
                                        $calc = (float)$employee->total_loan - (float)$get('payment_amount');
                                        $set('outstanding', (string)$calc);
                                        $set('total_loan', $employee->total_loan);
                                    } else {
                                        $set('total_loan', null);
                                        $set('outstanding', null);
                                    }
                                })
                                ->searchable()
                                ->preload(),
                            Forms\Components\TextInput::make('total_loan')
                                ->numeric()
                                ->disabled()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('payment_amount')
                                ->numeric()
                                ->required()
                                // ->reactive()
                                ->afterStateUpdated(function (Closure $set, callable $get, $state) {
                                    $calc = (float)$get('total_loan') - (float)$state;
                                    $set('outstanding', (string)$calc);
                                })
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('outstanding')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(false)
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\Select::make('coa_id_source')
                                ->label('Source')
                                ->required()
                                ->reactive()
                                ->preload()
                                ->searchable()
                                ->options(function () {
                                    $datas = Common::getViewCoaMasterDetails([
                                        ["level_first_id", "=", 1],
                                        ["level_second_code", "=", "03"],
                                    ])->get();
                                    return $datas->pluck('level_third_name', 'level_third_id');
                                }),
                            Forms\Components\Select::make('coa_id_destination')
                                ->label('Destination')
                                ->required()
                                ->preload()
                                ->reactive()
                                ->searchable()
                                ->options(function (callable $get) {
                                    $datas = Common::getViewCoaMasterDetails([
                                        ["level_first_id", "=", 1],
                                        ["level_second_code", "=", "01"],
                                    ])->get();
                                    return $datas->pluck('level_third_name', 'level_third_id');
                                }),

                        ]),
                    Card::make([
                        Placeholder::make('source_start_balance')
                            ->label('')
                            ->content(function (callable $get, ?Model $record) {
                                $num = CoaThird::find($get('coa_id_source'))->balance ?? 0;
                                return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                            }),
                        Placeholder::make('destination_start_balance')
                            ->label('')
                            ->content(function (callable $get, ?Model $record) {
                                $num = CoaThird::find($get('coa_id_destination'))->balance ?? 0;
                                return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                            }),

                    ])->columns(2),
                    Forms\Components\Textarea::make('description')
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_jurnal')->label('Post Journal')->boolean(),
                Tables\Columns\TextColumn::make('transaction_code'),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date(),
                Tables\Columns\TextColumn::make('employee.employee_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_loan')->money('idr', true),
                Tables\Columns\TextColumn::make('payment_amount')->money('idr', true),
                Tables\Columns\TextColumn::make('outstanding')->money('idr', true),
                Tables\Columns\TextColumn::make('coaThirdSource.fullname')
                    ->label('COA Source')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: false),
                Tables\Columns\TextColumn::make('coaThirdDestination.fullname')
                    ->label('COA Destination')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: false),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
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
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReceivables::route('/'),
            'create' => Pages\CreateReceivable::route('/create'),
            'view' => Pages\ViewReceivable::route('/{record}'),
            'edit' => Pages\EditReceivable::route('/{record}/edit'),
        ];
    }
}
