<?php

namespace App\Filament\Resources\CoaSecondResource\RelationManagers;

use App\Models\CoaSecond;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoaLevelThirdsRelationManager extends RelationManager
{
    protected static string $relationship = 'thirds';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $title = 'Level 3';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first.code')->label('level 1')->sortable(),
                Tables\Columns\TextColumn::make('second.code')->label('level 2')->sortable(),
                Tables\Columns\TextColumn::make('code')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form(function (CreateAction $action, RelationManager $livewire) {
                        $parentId = $livewire->ownerRecord->id;
                        return [
                            Grid::make(1)
                                ->schema([
                                    Forms\Components\Select::make('level_second_id')
                                        ->label('Level 2')
                                        ->required()
                                        // ->options(CoaSecond::where('level_first_id', '=', $parentId)->pluck('name', 'id'))
                                        ->relationship('second', 'name', fn (Builder $query) => $query->where('level_first_id', $parentId))
                                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                                        ->preload(),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('code')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                ]),
                        ];
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(function (EditAction $action, RelationManager $livewire) {
                        $parentId = $livewire->ownerRecord->id;
                        return [
                            Grid::make(1)
                                ->schema([
                                    Forms\Components\Select::make('level_second_id')
                                        ->label('Level 2')
                                        ->required()
                                        // ->options(CoaSecond::where('level_first_id', '=', $parentId)->pluck('name', 'id'))
                                        ->relationship('second', 'name', fn (Builder $query) => $query->where('level_first_id', $parentId))
                                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                                        ->preload(),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('code')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                ]),
                        ];
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery();
    }
}
