<?php

namespace App\Filament\Resources;

use App\Filament\Common\CoaMasterDetails;
use App\Filament\Common\Common;
use App\Filament\Resources\CashTransferResource\Pages;
use App\Filament\Resources\CashTransferResource\RelationManagers;
use App\Models\CashTransfer;
use App\Models\CoaThird;
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
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class CashTransferResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CashTransfer::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-copy';
    protected static ?string $slug = 'cash/cash-transfer';
    protected static ?string $navigationGroup = 'Cash';
    protected static ?string $navigationLabel = 'Cash Transfers';
    // protected static ?string $recordTitleAttribute = 'transaction_id';
    // protected static ?int $navigationSort = 3;

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
                            Forms\Components\TextInput::make('transaction_id')
                                ->label('Transaction ID')
                                ->required()
                                ->disabled()
                                ->maxLength(20)
                                ->default(fn () => Common::getNewCashTransferTransactionId()),
                            Forms\Components\DatePicker::make('transaction_date')
                                ->required()
                                ->default(Carbon::now()),
                            Forms\Components\Select::make('coa_id_source')
                                ->label('Source')
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
                                ->label('Destination')
                                ->required()
                                ->preload()
                                ->reactive()
                                ->searchable()
                                ->options(function (callable $get) {
                                    $datas = new Collection();
                                    if ($get('coa_id_source') != null) {
                                        $datas = Common::getViewCoaMasterDetails([
                                            ["level_first_id", "=", 1],
                                            ["level_second_code", "=", "01"],
                                            ['level_third_id', '!=', $get('coa_id_source')],
                                        ])->get();
                                    }
                                    return $datas->pluck('level_third_name', 'level_third_id');
                                }),
                        ]),
                    Card::make([
                        Placeholder::make('source_start_balance')
                            ->label('')
                            ->content(function (callable $get, ?Model $record) {
                                $num = CoaThird::find($get('coa_id_source'))->balance ?? 0;
                                if ($record) {
                                    $num = $record->source_start_balance;
                                }
                                return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                            }),
                        Placeholder::make('destination_start_balance')
                            ->label('')
                            ->content(function (callable $get, ?Model $record) {
                                $num = CoaThird::find($get('coa_id_destination'))->balance ?? 0;
                                if ($record) {
                                    $num = $record->destination_start_balance;
                                }
                                return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                            }),

                    ])->columns(2),
                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->mask(
                            fn (Mask $mask) => $mask
                                ->numeric()
                                ->decimalPlaces(2)
                                ->decimalSeparator(',')
                                ->thousandsSeparator(',')
                        )
                        ->columnSpanFull(),
                    Card::make([
                        Placeholder::make('source_end_balance')
                            ->label('Source End Balance')
                            ->content(function (callable $get, ?Model $record) {
                                $coaThird = CoaThird::find($get('coa_id_source'))->balance ?? 0;
                                $num = (float)$coaThird - (float)$get('amount');
                                if ($record) {
                                    $num = $record->source_end_balance;
                                }
                                return 'Rp ' . number_format($num, 0, ',', '.');
                            }),
                        Placeholder::make('destination_end_balance')
                            ->label('Destination End Balance')
                            ->content(function (callable $get, ?Model $record) {
                                $coaThird = CoaThird::find($get('coa_id_destination'))->balance ?? 0;
                                $num = (float)$coaThird + (float)$get('amount');
                                if ($record) {
                                    $num = $record->destination_end_balance;
                                }
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
                    ])->columns(2),
                    Forms\Components\Textarea::make('description')
                        ->maxLength(500)
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_jurnal')->label('Post Journal')->boolean(),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date(),
                Tables\Columns\TextColumn::make('amount')->money('idr', true),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_by')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
            ])
            ->filters([
                TextFilter::make('transaction_id'),
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
                ])
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
            'index' => Pages\ListCashTransfers::route('/'),
            'create' => Pages\CreateCashTransfer::route('/create'),
            'view' => Pages\ViewCashTransfer::route('/{record}'),
            'edit' => Pages\EditCashTransfer::route('/{record}/edit'),
        ];
    }
}
