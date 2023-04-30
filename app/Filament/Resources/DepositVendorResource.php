<?php

namespace App\Filament\Resources;

use App\Filament\Common\Common;
use App\Filament\Resources\DepositVendorResource\Pages;
use App\Filament\Resources\DepositVendorResource\RelationManagers;
use App\Models\CoaThird;
use App\Models\DepositVendor;
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
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class DepositVendorResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = DepositVendor::class;
    protected static ?string $navigationIcon = 'heroicon-o-library';
    protected static ?string $slug = 'cash/deposit-vendor';
    protected static ?string $navigationGroup = 'Cash';
    protected static ?string $navigationLabel = 'Deposit Vendors';
    protected static ?string $recordTitleAttribute = 'transaction_code';
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
                            Forms\Components\TextInput::make('transaction_code')
                                ->label('Transaction Code')
                                ->required()
                                ->disabled()
                                ->maxLength(20)
                                ->default(fn () => Common::getNewDepositVendorTransactionId()),
                            Forms\Components\DatePicker::make('transaction_date')
                                ->required(),
                            Forms\Components\Select::make('vendor_id')
                                ->label('vendor')
                                ->required()
                                ->reactive()
                                ->preload()
                                ->searchable()
                                ->relationship('vendor', 'name')
                                ->columnSpanFull(),
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
                                            ["level_second_code", "=", "02"],
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
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_code')
                    ->label('Transaction Code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date(),
                Tables\Columns\TextColumn::make('vendor.name'),
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
                TextFilter::make('transaction_code'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDepositVendors::route('/'),
            'create' => Pages\CreateDepositVendor::route('/create'),
            'view' => Pages\ViewDepositVendor::route('/{record}'),
            // 'edit' => Pages\EditDepositVendor::route('/{record}/edit'),
        ];
    }
}
