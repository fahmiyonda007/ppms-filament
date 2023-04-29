<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashTransferResource\Pages;
use App\Filament\Resources\CashTransferResource\RelationManagers;
use App\Models\CashTransfer;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CashTransferResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CashTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-copy';

    protected static ?string $slug = 'cash/cash-transfer';
    protected static ?string $navigationGroup = 'Cash';
    protected static ?string $navigationLabel = 'Cash Transfers';
    protected static ?string $recordTitleAttribute = 'transaction_id';
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
                                ->maxLength(20),
                            Forms\Components\DateTimePicker::make('transaction_date')
                                ->required(),
                            Forms\Components\Select::make('coa_id_source')
                                ->label('Source')
                                ->required()
                                ->reactive()
                                ->preload()
                                ->searchable()
                                ->relationship('coaThirdSource', 'name', function (Builder $query) {
                                    return $query->where('code', 'like', '1%');
                                }),
                            Forms\Components\Select::make('coa_id_destination')
                                ->label('Destination')
                                ->required()
                                ->preload()
                                ->searchable()
                                ->relationship('coaThirdDestination', 'name', function (Builder $query, callable $get) {
                                    if ($get('coa_id_source') == null) {
                                        $query->where('id', 0);
                                    } else {
                                        $query->where([['code', 'like', '1%'], ['id', '!=', $get('coa_id_source')]]);
                                    }
                                    return $query->where('code', 'like', '1%');
                                }),
                        ]),
                    Forms\Components\Textarea::make('description')
                        ->maxLength(500)
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id'),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('coa_id_source'),
                Tables\Columns\TextColumn::make('coa_id_destination'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_by'),
                Tables\Columns\TextColumn::make('updated_by'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCashTransfers::route('/'),
            'create' => Pages\CreateCashTransfer::route('/create'),
            'edit' => Pages\EditCashTransfer::route('/{record}/edit'),
        ];
    }
}
