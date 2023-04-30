<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use App\Filament\Resources\DepositVendorResource;
use App\Models\DepositVendor;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class DepositVendorRelationManager extends RelationManager
{
    protected static string $relationship = 'depositVendors';
    protected static ?string $title = 'History Deposit';
    protected static ?string $recordTitleAttribute = 'vendor_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_code')
                    ->label('Transaction Code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date(),
                Tables\Columns\TextColumn::make('vendor.name'),
                Tables\Columns\TextColumn::make('amount')->money('idr', true),
                Tables\Columns\TextColumn::make('coaThirdSource.fullname')
                    ->label('COA Source')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: true),
                Tables\Columns\TextColumn::make('source_start_balance')->money('idr', true),
                Tables\Columns\TextColumn::make('source_end_balance')->money('idr', true),
                Tables\Columns\TextColumn::make('coaThirdDestination.fullname')
                    ->label('COA Destination')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: true),
                Tables\Columns\TextColumn::make('destination_start_balance')->money('idr', true),
                Tables\Columns\TextColumn::make('destination_end_balance')->money('idr', true),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_by')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
            ])
            ->filters([
                TextFilter::make('transaction_code'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->url(fn ($record): string => DepositVendorResource::getUrl('create'))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record): string => DepositVendorResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
