<?php

namespace App\Filament\Resources\ProjectPlanResource\RelationManagers;

use App\Filament\Resources\ProjectCostResource;
use App\Models\ProjectCost;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;
use Illuminate\Support\Str;

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
                Tables\Columns\TextColumn::make('projectPlan.name')->sortable()->searchable(['project_plans.name']),
                Tables\Columns\TextColumn::make('transaction_code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->color(fn (Model $record) => $record->payment_status == 'PAID' ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendor.name')->sortable(),
                Tables\Columns\TextColumn::make('total_payment')->money('idr', true)->sortable()
                    ->color(fn ($record) => $record->total_amount > $record->total_payment ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('total_amount')->money('idr', true)->sortable(),
                Tables\Columns\TextColumn::make('coaThird1.fullname')
                    ->label('Payment Source 1')->sortable(['name']),
                Tables\Columns\TextColumn::make('coaThird2.fullname')
                    ->label('Payment Source 2')->sortable(['name']),
                Tables\Columns\TextColumn::make('coaThird3.fullname')
                    ->label('Payment Source 3')->sortable(['name']),
            ])
            ->filters([
                TextFilter::make('payment_status'),
                SelectFilter::make('projectPlan.name'),
                SelectFilter::make('vendor.name'),
                TextFilter::make('transaction_code'),
                DateFilter::make('payment_date'),
                DateFilter::make('order_date'),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (ProjectCost $record): string => env('APP_URL') . '/admin/project/costs/' . $record->id, true),
                Tables\Actions\EditAction::make()
                    ->visible(function (Model $record) {
                        return $record->payment_status == "NOT PAID";
                    })
                    ->url(fn (ProjectCost $record): string => env('APP_URL') . '/admin/project/costs/' . $record->id . '/edit', true),
                Tables\Actions\DeleteAction::make()
                    ->visible(function (Model $record) {
                        return $record->payment_status == "NOT PAID";
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
