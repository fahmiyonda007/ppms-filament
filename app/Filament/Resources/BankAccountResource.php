<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Filament\Resources\BankAccountResource\RelationManagers;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $slug = 'master/bankaccounts';
    protected static ?string $navigationGroup = 'Masters';
    protected static ?string $navigationLabel = 'Bank Accounts';
    protected static ?string $recordTitleAttribute = 'account_name';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    Grid::make(1)
                        ->schema([
                            Select::make('bank_id')
                                ->multiple(false)
                                ->relationship('banks', 'bank_name')->preload(),
                        ]),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('account_number')
                                ->required()
                                ->maxLength(255)
                                ->numeric(),
                            TextInput::make('account_name')
                                ->required()
                                ->maxLength(255),
                        ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('banks.bank_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('banks.bank_name'),
                Filter::make('account_number'),
                Filter::make('account_name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
