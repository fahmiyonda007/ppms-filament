<?php

namespace App\Filament\Resources\CoaFirstResource\RelationManagers;

use App\Models\CoaFirst;
use App\Models\CoaSecond;
use App\Models\CoaThird;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;


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
                Tables\Columns\TextColumn::make('first.fullname')->label('level 1'),
                Tables\Columns\TextColumn::make('second.fullname')->label('level 2'),
                Tables\Columns\TextColumn::make('fullname')->label('level 3')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('name', 'like', "%{$search}%");
                    }),
                Tables\Columns\TextColumn::make('balance')
                    ->sortable()
                    ->money('idr', true)->alignRight(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('New')
                    ->using(function (HasRelationshipTable $livewire, array $data): Model {
                        $data['created_by'] = auth()->user()->email;
                        return $livewire->getRelationship()->create($data);
                    })
                    ->form(function (CreateAction $action, RelationManager $livewire) {
                        $parentId = $livewire->ownerRecord->id;
                        return [
                            Grid::make(1)
                                ->schema([
                                    Forms\Components\Select::make('level_second_id')
                                        ->label('Level 2')
                                        ->required()
                                        ->relationship('second', 'name', fn (Builder $query) => $query->where('level_first_id', $parentId))
                                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                                        ->preload()
                                        ->searchable()
                                        ->reactive()
                                        ->afterStateUpdated(function (Closure $set, $state) {
                                            $second = CoaSecond::find($state);
                                            if ($second) {
                                                $thirds = CoaThird::where([
                                                    ['level_first_id', '=', $second->level_first_id],
                                                    ['level_second_id', '=', $second->id]
                                                ]);
                                                $first = CoaFirst::find($second->level_first_id);
                                                $len = str_pad($thirds->count() + 1, 3, '0', STR_PAD_LEFT);
                                                $numb =  "{$first->code}{$second->code}{$len}";
                                                $set('code', $numb);
                                            } else {
                                                $set('code', '');
                                            }
                                        }),
                                ]),
                            Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('code')
                                        ->required()
                                        ->disabled(),
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('balance')
                                        ->disabled()
                                        ->numeric()
                                        ->mask(
                                            fn (Mask $mask) => $mask
                                                ->numeric()
                                                ->thousandsSeparator(',')
                                        ),
                                ]),
                        ];
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function (Model $record, array $data): Model {
                        $data['updated_by'] = auth()->user()->email;
                        $record->update($data);
                        return $record;
                    })
                    ->form(function (EditAction $action, RelationManager $livewire) {
                        $parentId = $livewire->ownerRecord->id;
                        return [
                            Grid::make(1)
                                ->schema([
                                    Forms\Components\Select::make('level_second_id')
                                        ->label('Level 2')
                                        ->required()
                                        ->relationship('second', 'name', fn (Builder $query) => $query->where('level_first_id', $parentId))
                                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                                        ->preload()
                                        ->searchable()
                                        ->reactive()
                                        ->afterStateUpdated(function ($record, Closure $set, $state) {
                                            $second = CoaSecond::find($state);
                                            if ($second) {
                                                if ($record->level_second_id <> $second->id) {
                                                    $thirds = CoaThird::where([
                                                        ['level_first_id', '=', $second->level_first_id],
                                                        ['level_second_id', '=', $second->id]
                                                    ]);
                                                    $first = CoaFirst::find($second->level_first_id);
                                                    $len = str_pad($thirds->count() + 1, 3, '0', STR_PAD_LEFT);
                                                    $numb =  "{$first->code}{$second->code}{$len}";
                                                    $set('code', $numb);
                                                } else if ($record->level_second_id == $second->id) {
                                                    $set('code', $record->code);
                                                }
                                            } else {
                                                $set('code', '');
                                            }
                                        }),
                                ]),
                            Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('code')
                                        ->required()
                                        ->disabled()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('balance')
                                        ->disabled()
                                        ->numeric()
                                        ->mask(
                                            fn (Mask $mask) => $mask
                                                ->numeric()
                                                ->thousandsSeparator(',')
                                        ),
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

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 20];
    }
}
