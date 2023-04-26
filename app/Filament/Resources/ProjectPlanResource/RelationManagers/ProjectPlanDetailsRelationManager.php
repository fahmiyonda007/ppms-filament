<?php

namespace App\Filament\Resources\ProjectPlanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Card;
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
use Illuminate\Support\Str;


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
                Card::make([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('booking_by')
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->preload()
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - {$record->phone}")
                                ->label('Booking By'),
                            Forms\Components\DatePicker::make('booking_date'),
                            Forms\Components\TextInput::make('deal_price')
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\Select::make('payment_type')
                                ->options([
                                    'TUNAI' => 'TUNAI',
                                    'TUNAI BERTAHAP' => 'TUNAI BERTAHAP',
                                    'KPR' => 'KPR',
                                ]),
                            Forms\Components\TextInput::make('down_payment')
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('tax')
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('notary_fee')
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('commission')
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('other_commission')
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('net_price')
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                        ]),
                    Forms\Components\Select::make('sales_id')
                        ->relationship(
                            'employee',
                            'employee_name',
                            fn (Builder $query) => $query
                                ->where('department', 'SALES')
                                ->Where('is_resign', 0)
                        )
                        ->searchable()
                        ->preload()
                        // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->empname} - {$record->phone}")
                        ->label('Sales'),
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit_kavling')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')->money('idr', true),
                Tables\Columns\TextColumn::make('customer.name')->label('Booking By'),
                Tables\Columns\TextColumn::make('unit_price')->money('idr', true),
                Tables\Columns\TextColumn::make('net_price')->money('idr', true),
                Tables\Columns\TextColumn::make('deal_price')->money('idr', true),
            ])
            ->filters([
                TextFilter::make('unit_kavling')
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(function (RelationManager $livewire) {
                        return Str::contains(url()->current(), '/edit') && $livewire->ownerRecord->progress < 100.0;
                    })
                    ->using(function (HasRelationshipTable $livewire, array $data): Model {
                        $data['created_by'] = auth()->user()->email;
                        return $livewire->getRelationship()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(function (RelationManager $livewire) {
                        return Str::contains(url()->current(), '/edit') && $livewire->ownerRecord->progress < 100.0;
                    })
                    ->using(function (Model $record, array $data): Model {
                        $data['updated_by'] = auth()->user()->email;
                        $record->update($data);
                        return $record;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function (RelationManager $livewire) {
                        return Str::contains(url()->current(), '/edit') && $livewire->ownerRecord->progress < 100.0;
                    }),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(function (RelationManager $livewire) {
                        return Str::contains(url()->current(), '/edit') && $livewire->ownerRecord->progress < 100.0;
                    }),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        // dd($ownerRecord->progress);
        return true;
        // return $ownerRecord->progress < 100.0;
    }
}
