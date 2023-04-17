<?php

namespace App\Filament\Resources\ProjectPlanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class ProjectPlanDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectPlanDetails';

    // protected static ?string $recordTitleAttribute = 'project_plan_id';
    protected static ?string $title = 'Details';
    protected static ?string $label = 'Details';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('unit_kavling')
                            ->required()
                            ->maxLength(20),
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
                    ]),
                Grid::make(1)
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->maxLength(2000),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit_kavling')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')->money('idr'),
                Tables\Columns\TextColumn::make('booking_by'),
            ])
            ->filters([
                TextFilter::make('unit_kavling')
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
