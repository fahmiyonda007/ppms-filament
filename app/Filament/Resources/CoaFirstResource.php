<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoaFirstResource\Pages;
use App\Filament\Resources\CoaFirstResource\RelationManagers\CoaLevelSecondsRelationManager;
use App\Filament\Resources\CoaFirstResource\RelationManagers\CoaLevelThirdsRelationManager;
use App\Filament\Resources\CoaThirdResource\Pages\ListCoaThirds;
use App\Models\CoaFirst;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;


class CoaFirstResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CoaFirst::class;
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?string $slug = 'master/coa';
    protected static ?string $navigationGroup = 'Masters';
    protected static ?string $navigationLabel = 'COA';
    // protected static ?int $navigationSort = 1;
    protected static ?string $label = 'C O A ';
    // protected static bool $shouldRegisterNavigation = false;
    protected static ?string $recordTitleAttribute = 'name';


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
                // TextColumn::make('code'),
                // TextColumn::make('thirds.name'),
                TextColumn::make('first')
                    ->sortable()
                    // ->searchable(['coa_level_firsts.code', 'coa_level_firsts.name']),
                    ->searchable(isGlobal: false, isIndividual: true, query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('coa_level_firsts.name', 'like', "%{$search}%");
                    }),
                TextColumn::make('second')
                    ->sortable()
                    // ->searchable(['coa_level_seconds.code', 'coa_level_seconds.name']),
                    ->searchable(isGlobal: false, isIndividual: true, query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('coa_level_seconds.name', 'like', "%{$search}%");
                    }),
                TextColumn::make('third')
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true, query: function (Builder $query, string $search): Builder {
                        // dd($query->get());
                        $qry = $query->where('coa_level_thirds.name', 'like', "%{$search}%");
                        //  dd($qry->toSql());
                        return $qry;
                    }),
                // ->searchable(['coa_level_thirds.code', 'coa_level_thirds.name'])
            ])
            ->filters([
                // TextFilter::make('third')->query()
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Model $record): string => CoaFirstResource::getUrl('index') . '/' . $record->id . '/edit?activeRelationManager=1'),
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
            'index' => ListCoaThirds::route('/'),
            'create' => Pages\CreateCoaFirst::route('/create'),
            'edit' => Pages\EditCoaFirst::route('/{record}/edit'),
        ];
    }
}
