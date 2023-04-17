<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
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
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class CustomerResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $slug = 'master/customers';
    protected static ?string $navigationGroup = 'Masters';
    protected static ?string $navigationLabel = 'Customers';
    protected static ?string $recordTitleAttribute = 'name';
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
                            Forms\Components\TextInput::make('code')
                                ->maxLength(10)
                                ->unique(ignoreRecord: true)
                                ->disabled()
                                ->required()
                                ->default(function () {
                                    $customers = Customer::all();
                                    $len = str_pad($customers->count() + 1, 5, '0', STR_PAD_LEFT);
                                    return "CUST" . $len;
                                }),
                            Forms\Components\TextInput::make('name')
                                ->maxLength(50)
                                ->required(),
                        ]),
                    Grid::make(1)
                        ->schema([
                            Forms\Components\TextInput::make('phone')
                                ->tel()
                                ->maxLength(50)
                                ->required(),
                            Forms\Components\Textarea::make('address')
                                ->maxLength(100)
                                ->required(),
                        ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                TextFilter::make('name')
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
