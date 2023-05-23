<?php

namespace App\Filament\Resources\ReceivableResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReceivablesRelationManager extends RelationManager
{
    protected static string $relationship = 'receivables';
    protected static ?string $title = 'Receivables History';

    // protected static ?string $recordTitleAttribute = 'loan_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('loan_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_jurnal')->label('Post Journal')->boolean(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date(),
                Tables\Columns\TextColumn::make('loan.transaction_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_loan')->money('idr', true),
                Tables\Columns\TextColumn::make('payment_amount')->money('idr', true),
                Tables\Columns\TextColumn::make('outstanding')->money('idr', true),
                Tables\Columns\TextColumn::make('coaThirdSource.fullname')
                    ->label('COA Source')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: false),
                Tables\Columns\TextColumn::make('coaThirdDestination.fullname')
                    ->label('COA Destination')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: false),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 20];
    }
}
