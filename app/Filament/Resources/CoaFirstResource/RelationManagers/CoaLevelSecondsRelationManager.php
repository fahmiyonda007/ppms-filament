<?php

namespace App\Filament\Resources\CoaFirstResource\RelationManagers;

use App\Models\CoaFirst;
use App\Models\CoaSecond;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoaLevelSecondsRelationManager extends RelationManager
{
    protected static string $relationship = 'seconds';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $title = 'Level 2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->default(function (RelationManager $livewire) {
                        $firstId = $livewire->ownerRecord->id;
                        $seconds = CoaSecond::where('level_first_id', $firstId)->get();
                        $len = str_pad($seconds->count() + 1, 2, '0', STR_PAD_LEFT);
                        return $len;
                    }),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first.fullname')->label('level 1'),
                Tables\Columns\TextColumn::make('fullname')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('name', 'like', "%{$search}%");
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('New')
                    ->using(function (HasRelationshipTable $livewire, array $data): Model {
                        $data['created_by'] = auth()->user()->email;
                        return $livewire->getRelationship()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function (Model $record, array $data): Model {
                        $data['updated_by'] = auth()->user()->email;
                        $record->update($data);
                        return $record;
                    }),
                // Tables\Actions\DeleteAction::make()
                //     ->before(function ($record) {
                //         $record->thirds()->where('level_second_id', $record->id)->delete();
                //     }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 20];
    }
}
