<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SysLookupResource\Pages;
use App\Filament\Resources\SysLookupResource\RelationManagers;
use App\Models\SysLookup;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class SysLookupResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = SysLookup::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $slug = 'settings/syslookups';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'System Lookups';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 5;


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
                    Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('group_name')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('code')
                                ->maxLength(50),
                            Forms\Components\TextInput::make('name')
                                ->maxLength(255),
                        ]),
                    Forms\Components\Textarea::make('description')
                        ->maxLength(1000),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group_name')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                SelectFilter::make('group_name')
                    ->searchable()
                    ->options(SysLookup::select('group_name')->groupBy('group_name')->pluck('group_name', 'group_name'))
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
            'index' => Pages\ListSysLookups::route('/'),
            'create' => Pages\CreateSysLookup::route('/create'),
            'edit' => Pages\EditSysLookup::route('/{record}/edit'),
        ];
    }
}
