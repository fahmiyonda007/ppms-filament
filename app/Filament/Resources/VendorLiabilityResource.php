<?php

namespace App\Filament\Resources;

use App\Filament\Common\Common;
use App\Filament\Resources\VendorLiabilityResource\Pages;
use App\Filament\Resources\VendorLiabilityResource\RelationManagers;
use App\Filament\Resources\VendorLiabilityResource\RelationManagers\VendorLiabilityPaymentsRelationManager;
use App\Models\VendorLiability;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentAdvancedFilter\Filters\BooleanFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class VendorLiabilityResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = VendorLiability::class;
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?string $slug = 'cash/vendor-liabiities';
    protected static ?string $navigationGroup = 'Cash';
    protected static ?string $navigationLabel = 'Vendor Liabilities';
    // protected static ?int $navigationSort = 2;

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('project_plan_id')
                                ->relationship(
                                    'projectPlan',
                                    'name',
                                    fn(Builder $query) => $query->whereNotIn('id', [1, 2, 3])
                                )
                                ->preload()
                                ->required()
                                ->searchable(),
                            Forms\Components\TextInput::make('transaction_code')
                                ->maxLength(20)
                                ->required()
                                ->disabled()
                                ->default(fn() => Common::getNewVendorLiabilityTransactionId()),
                            Forms\Components\DatePicker::make('transaction_date')
                                ->required()
                                ->default(Carbon::now()),
                            Forms\Components\Select::make('vendor_id')
                                ->relationship('vendor', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Forms\Components\TextInput::make('scope_of_work')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('est_price')
                                ->numeric()
                                ->required()
                                ->reactive()
                                ->mask(
                                    fn(Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                )
                                ->afterStateUpdated(function (Closure $set, $state, $record) {
                                    $set('deal_price', $state);
                                    $amtDet = 0;
                                    if ($record) {
                                        $amtDet = (float) $record->vendorLiabilityPayments->sum('amount');
                                    }
                                    $calc = (float) $state - $amtDet;
                                    $set('outstanding', (string) $calc);
                                }),
                            Forms\Components\TextInput::make('deal_price')
                                ->numeric()
                                ->reactive()
                                ->required()
                                ->mask(
                                    fn(Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                )
                                ->afterStateUpdated(function (Closure $set, $state, $record) {
                                    $amtDet = 0;
                                    if ($record) {
                                        $amtDet = (float) $record->vendorLiabilityPayments->sum('amount');
                                    }
                                    $calc = (float) $state - $amtDet;
                                    $set('outstanding', (string) $calc);
                                }),
                            Forms\Components\DatePicker::make('start_date')
                                ->reactive()
                                ->required()
                                ->default(Carbon::now()->startOfMonth())
                                ->maxDate(function (callable $get) {
                                    if ($get('end_date')) {
                                        return new Carbon($get('end_date'));
                                    }
                                })
                                ->afterStateUpdated(function (Closure $set, callable $get, $state) {
                                    $start = Carbon::parse($state);
                                    $end = Carbon::parse($get('est_end_date'));
                                    $diff = $start->diffInDays($end);
                                    if ($get('salary_type') == 'DAILY') {
                                        $set('total_days', $diff);
                                    }
                                }),
                            Forms\Components\DatePicker::make('est_end_date')
                                ->reactive()
                                ->required()
                                ->default(Carbon::now()->endOfMonth())
                                ->minDate(function (callable $get) {
                                    if ($get('start_date')) {
                                        return new Carbon($get('start_date'));
                                    }
                                })
                                ->afterStateUpdated(function (Closure $set, callable $get, $state) {
                                    $start = Carbon::parse($get('start_date'));
                                    $end = Carbon::parse($state);
                                    $diff = $start->diffInDays($end);
                                    if ($get('salary_type') == 'DAILY') {
                                        $set('total_days', $diff);
                                    }
                                }),
                            Forms\Components\DatePicker::make('end_date'),
                            Forms\Components\TextInput::make('outstanding')
                                ->numeric()
                                ->required()
                                ->disabled()
                                ->mask(
                                    fn(Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\Textarea::make('description')
                                ->maxLength(500)
                                ->columnSpanFull(),
                            Forms\Components\Radio::make('project_status')
                                ->label('Status?')
                                ->disabled()
                                ->options([
                                    0 => 'NOT DONE',
                                    1 => 'DONE',
                                ])
                                ->inline()
                                ->default('0')
                                ->columnSpanFull(),
                        ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\BadgeColumn::make('project_status')
                    ->formatStateUsing(function (string $state): string {
                        $type = $state == '0' ? 'NOT DONE' : 'DONE';
                        return $type;
                    })
                    ->color(function (string $state): string {
                        $type = $state == '0' ? 'danger' : 'success';
                        return $type;
                    }),
                Tables\Columns\TextColumn::make('projectPlan.name')
                    ->sortable(['name'])
                    ->searchable(['name']),
                Tables\Columns\TextColumn::make('vendor.name')->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date(),
                Tables\Columns\TextColumn::make('transaction_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scope_of_work')
                    ->searchable(),
                Tables\Columns\TextColumn::make('est_price')->money('idr', true),
                Tables\Columns\TextColumn::make('deal_price')->money('idr', true),
                Tables\Columns\TextColumn::make('start_date')
                    ->date(),
                Tables\Columns\TextColumn::make('est_end_date')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date(),
                Tables\Columns\TextColumn::make('outstanding')->money('idr', true),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                SelectFilter::make('project_status')
                    ->options([
                        0 => 'NOT DONE',
                        1 => 'DONE',
                    ]),
                TextFilter::make('scope_of_work'),
                DateFilter::make('start_date'),
                DateFilter::make('end_date'),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(function ($record) {
                            if ($record) {
                                if ($record->project_status == 1) {
                                    return false;
                                }
                                return true;
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VendorLiabilityPaymentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendorLiabilities::route('/'),
            'create' => Pages\CreateVendorLiability::route('/create'),
            'view' => Pages\ViewVendorLiability::route('/{record}'),
            'edit' => Pages\EditVendorLiability::route('/{record}/edit'),
        ];
    }
}
