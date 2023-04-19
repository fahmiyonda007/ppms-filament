<?php

namespace App\Filament\Resources\ProjectCostResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectCostDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectCostDetails';
    // protected static ?string $recordTitleAttribute = 'project_cost_id';
    protected static ?string $title = 'Details';
    protected static ?string $label = 'Details';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('coa_id')
                    ->relationship('coaThird', 'name', fn (Builder $query) => $query->where('code', 'like', '5%'))
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                    ->required()
                    ->preload(),
                Forms\Components\TextInput::make('uom')
                    ->required(),
                Forms\Components\TextInput::make('qty')
                    ->required()
                    ->numeric()
                    ->mask(
                        fn (Mask $mask) => $mask
                            ->numeric()
                            ->thousandsSeparator(',')
                    ),
                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->mask(
                        fn (Mask $mask) => $mask
                            ->numeric()
                            ->decimalPlaces(2)
                            ->decimalSeparator(',')
                            ->thousandsSeparator(',')
                    ),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->mask(
                        fn (Mask $mask) => $mask
                            ->numeric()
                            ->decimalPlaces(2)
                            ->decimalSeparator(',')
                            ->thousandsSeparator(',')
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('coaThird.fullname')->label('COA'),
                Tables\Columns\TextColumn::make('uom'),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('unit_price')->money('idr'),
                Tables\Columns\TextColumn::make('amount')->money('idr'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
