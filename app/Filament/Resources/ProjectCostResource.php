<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectCostResource\Pages;
use App\Filament\Resources\ProjectCostResource\RelationManagers;
use App\Filament\Resources\ProjectCostResource\RelationManagers\ProjectCostDetailsRelationManager;
use App\Models\ProjectCost;
use App\Models\ProjectPlan;
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

class ProjectCostResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ProjectCost::class;
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?string $slug = 'project/costs';
    protected static ?string $navigationGroup = 'Projects';
    protected static ?string $navigationLabel = 'Costs';
    protected static ?string $recordTitleAttribute = 'transaction_code';
    protected static ?int $navigationSort = 2;


    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('transaction_code')
                                ->maxLength(25)
                                ->unique(ignoreRecord: true)
                                ->disabled()
                                ->required(),
                            Forms\Components\TextInput::make('payment_status')
                                ->disabled()
                                ->required()
                                ->default('NOT PAID')
                                ->maxLength(50),
                        ]),


                    Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('project_plan_id')
                                ->relationship('projectPlan', 'name')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, $state) {
                                    $projectCosts = ProjectCost::where('project_plan_id', $state);
                                    $len = str_pad($projectCosts->count() + 1, 3, '0', STR_PAD_LEFT);
                                    $set('transaction_code', 'TRX-' . $state . '-' . $len);
                                }),
                            Forms\Components\Select::make('vendor_id')
                                ->relationship('vendor', 'name')
                                ->required(),
                            Forms\Components\DatePicker::make('order_date')
                                ->required(),
                            Forms\Components\DatePicker::make('payment_date')
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, $state) {
                                    if ($state) {
                                        $set('payment_status', 'PAID');
                                    } else {
                                        $set('payment_status', 'NOT PAID');
                                    }
                                }),
                        ]),
                    Grid::make(1)
                        ->schema([
                            Forms\Components\RichEditor::make('description')
                                ->maxLength(500),
                        ]),
                ]),
            ]);
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProjectCostDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectCosts::route('/'),
            'create' => Pages\CreateProjectCost::route('/create'),
            'edit' => Pages\EditProjectCost::route('/{record}/edit'),
        ];
    }
}
