<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoaFirstResource\Pages;
use App\Filament\Resources\CoaFirstResource\RelationManagers;
use App\Filament\Resources\CoaSecondResource\RelationManagers\CoaLevelSecondsRelationManager;
use App\Filament\Resources\CoaSecondResource\RelationManagers\CoaLevelThirdsRelationManager;
use App\Models\CoaFirst;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;


class CoaFirstResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CoaFirst::class;
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?string $slug = 'master/coa';
    protected static ?string $navigationGroup = 'Masters';
    protected static ?string $navigationLabel = 'COA';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'C O A ';


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
                            TextInput::make('code')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            TextInput::make('name')
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
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                TextFilter::make('code'),
                TextFilter::make('name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        $record->seconds()->where('level_first_id', $record->id)->delete();
                        $record->thirds()->where('level_first_id', $record->id)->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($records) {
                        foreach ($records as $item) {
                            $item->seconds()->where('level_first_id', $item->id)->delete();
                            $item->thirds()->where('level_first_id', $item->id)->delete();
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CoaLevelSecondsRelationManager::class,
            CoaLevelThirdsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoaFirsts::route('/'),
            'create' => Pages\CreateCoaFirst::route('/create'),
            'edit' => Pages\EditCoaFirst::route('/{record}/edit'),
        ];
    }
}
