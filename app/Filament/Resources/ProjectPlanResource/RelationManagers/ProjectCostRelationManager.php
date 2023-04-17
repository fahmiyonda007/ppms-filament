<?php

namespace App\Filament\Resources\ProjectPlanResource\RelationManagers;

use App\Filament\Resources\ProjectCostResource;
use App\Models\ProjectCost;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectCostRelationManager extends RelationManager
{
    protected static string $relationship = 'projectCost';
    // protected static ?string $recordTitleAttribute = 'project_plan_id';
    protected static ?string $title = 'Project Costs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('projectPlan.name'),
                Tables\Columns\TextColumn::make('transaction_code'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('order_date')
                    ->date(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date(),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->color(fn (Model $record) => $record->payment_status == 'PAID' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('vendor.name'),
                Tables\Columns\TextColumn::make('coa_id_source1'),
                Tables\Columns\TextColumn::make('coa_id_source2'),
                Tables\Columns\TextColumn::make('coa_id_source3'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (ProjectCost $record): string => env('APP_URL') . '/admin/project/costs/' . $record->id . '/edit'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
