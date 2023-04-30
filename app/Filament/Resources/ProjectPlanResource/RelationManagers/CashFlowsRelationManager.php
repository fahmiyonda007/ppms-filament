<?php

namespace App\Filament\Resources\ProjectPlanResource\RelationManagers;

use App\Filament\Resources\CashFlowResource;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CashFlowsRelationManager extends RelationManager
{
    protected static string $relationship = 'cashFlows';
    protected static ?string $title = 'Cash Flow History';
    // protected static ?string $recordTitleAttribute = 'project_plan_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_code'),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date(),
                Tables\Columns\TextColumn::make('projectPlan.name')
                    ->sortable(['name'])
                    ->searchable(['name']),
                Tables\Columns\TextColumn::make('coaThird.fullname')
                    ->sortable(['name'])
                    ->searchable(['name']),
                Tables\Columns\TextColumn::make('cash_flow_type')
                    ->enum([
                        'CASH_IN' => 'CASH IN',
                        'CASH_OUT' => 'CASH OUT',
                    ]),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->url(fn ($record): string => CashFlowResource::getUrl('create'))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn ($record): string => CashFlowResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
