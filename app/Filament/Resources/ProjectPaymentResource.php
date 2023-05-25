<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectPaymenteResource\RelationManagers\ProjectPaymentDetailsRelationManager;
use App\Filament\Resources\ProjectPaymentResource\Pages;
use App\Filament\Resources\ProjectPaymentResource\RelationManagers;
use App\Models\ProjectPayment;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectPaymentResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ProjectPayment::class;
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?string $slug = 'project/payments';
    protected static ?string $navigationGroup = 'Projects';
    protected static ?string $navigationLabel = 'Payments';
    protected static ?int $navigationSort = 2;

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
                                ->relationship('projectPlan', 'name')
                                ->reactive()
                                ->preload()
                                ->searchable()
                                ->afterStateUpdated(function (Closure $set, $state) {
                                    $set('project_plan_detail_id', null);
                                }),
                            Forms\Components\Select::make('project_plan_detail_id')
                                ->relationship(
                                    'projectPlanDetail',
                                    'unit_kavling',
                                    fn (Builder $query, callable $get) => $query->where('project_plan_id', $get('project_plan_id'))
                                )
                                ->preload()
                                ->searchable(),
                            Forms\Components\Select::make('booking_by')
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->preload()
                                ->reactive()
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - {$record->phone}")
                                ->label('Booking By'),
                            Forms\Components\DatePicker::make('booking_date')
                                ->required(function (callable $get) {
                                    return $get('booking_by') != null;
                                }),
                            Forms\Components\Select::make('payment_type')
                                ->required(function (callable $get) {
                                    return $get('booking_by') != null;
                                })
                                ->searchable()
                                ->reactive()
                                ->options([
                                    'TUNAI' => 'TUNAI',
                                    'TUNAI BERTAHAP' => 'TUNAI BERTAHAP',
                                    'KPR' => 'KPR',
                                ]),
                            Forms\Components\Select::make('kpr_type')
                                ->label('KPR Type')
                                ->required(function (callable $get) {
                                    return $get('booking_by') != null;
                                })
                                ->reactive()
                                ->searchable()
                                ->required(function (callable $get) {
                                    return $get('payment_type') == 'KPR';
                                })
                                ->visible(function (callable $get) {
                                    return $get('payment_type') == 'KPR';
                                })
                                ->options([
                                    'PKS' => 'PKS',
                                    'NON PKS' => 'NON PKS',
                                ]),
                            Forms\Components\Select::make('sales_id')
                                ->relationship(
                                    'employee',
                                    'employee_name',
                                    fn (Builder $query) => $query
                                        ->where('department', 'MARKETING')
                                        ->Where('is_resign', 1)
                                )
                                ->required(function (callable $get) {
                                    return $get('booking_by') != null;
                                })
                                ->searchable()
                                ->preload()
                                ->columnSpanFull()
                                ->label('Sales'),
                            Forms\Components\Textarea::make('description')
                                ->columnSpanFull()
                                ->maxLength(2000),
                        ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('projectPlan.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('projectPlanDetail.unit_kavling')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name'),
                Tables\Columns\TextColumn::make('payment_type'),
                Tables\Columns\TextColumn::make('kpr_type'),
                Tables\Columns\TextColumn::make('employee.employee_name')
                    ->label('Sales'),
                Tables\Columns\TextColumn::make('description')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProjectPaymentDetailsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectPayments::route('/'),
            'create' => Pages\CreateProjectPayment::route('/create'),
            'view' => Pages\ViewProjectPayment::route('/{record}'),
            'edit' => Pages\EditProjectPayment::route('/{record}/edit'),
        ];
    }
}
