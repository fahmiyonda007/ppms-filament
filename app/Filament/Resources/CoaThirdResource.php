<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoaThirdResource\Pages;
use App\Filament\Resources\CoaThirdResource\RelationManagers;
use App\Models\CoaThird;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoaThirdResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CoaThird::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $label = 'C O A ';
    protected static bool $shouldRegisterNavigation = false;


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
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first.fullname')
                    ->sortable()
                    ->searchable(['name'], isIndividual: true),
                Tables\Columns\TextColumn::make('second.fullname')
                    ->sortable()
                    ->searchable(['name'], isIndividual: true),
                Tables\Columns\TextColumn::make('fullname')
                    ->sortable()
                    ->searchable(['name'], isIndividual: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => CoaFirstResource::getUrl('index') . '/' . $record->level_first_id . '/edit?activeRelationManager=1'),
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
            'index' => Pages\ListCoaThirds::route('/'),
            // 'create' => Pages\CreateCoaThird::route('/create'),
            // 'edit' => Pages\EditCoaThird::route('/{record}/edit'),
        ];
    }
}
