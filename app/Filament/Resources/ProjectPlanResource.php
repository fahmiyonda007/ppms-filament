<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\ProjectCostResource\RelationManagers\ProjectCostDetailsRelationManager;
use App\Filament\Resources\ProjectPlanResource\Pages;
use App\Filament\Resources\ProjectPlanResource\RelationManagers;
use App\Filament\Resources\ProjectPlanResource\RelationManagers\CashFlowsRelationManager;
use App\Filament\Resources\ProjectPlanResource\RelationManagers\ProjectCostRelationManager;
use App\Filament\Resources\ProjectPlanResource\RelationManagers\ProjectPlanDetailsRelationManager;
use App\Models\ProjectPlan;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use RyanChandler\FilamentProgressColumn\ProgressColumn;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\NumberFilter;

class ProjectPlanResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ProjectPlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $slug = 'project/plans';
    protected static ?string $navigationGroup = 'Projects';
    protected static ?string $navigationLabel = 'Project Plans';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 1;


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
                            Forms\Components\TextInput::make('code')
                                ->required()
                                ->maxLength(25)
                                ->unique(ignoreRecord: true)
                                ->disabled()
                                ->required()
                                ->default(function () {
                                    $projectsPlan = ProjectPlan::all();
                                    $len = str_pad($projectsPlan->count() + 1, 3, '0', STR_PAD_LEFT);
                                    return "PRJ" . $len;
                                }),
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(50),
                        ]),
                    Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('start_project')
                                ->required(),
                            Forms\Components\DatePicker::make('est_end_project')
                                ->required(),
                            Forms\Components\DatePicker::make('end_project'),
                            Forms\Components\TextInput::make('progress')
                                ->numeric()
                                ->maxValue(100)
                                ->default(0),
                        ]),
                    Forms\Components\RichEditor::make('description')
                        ->maxLength(1000),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->hidden(),
                ProgressColumn::make('progress')
                    ->sortable()
                    ->color('bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500'),
                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_project')
                    ->date(),
                Tables\Columns\TextColumn::make('est_end_project')
                    ->date(),
                Tables\Columns\TextColumn::make('end_project')
                    ->date(),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    // ->extraViewData(fn ($action) => [
                    //     'recordCount' => $action->getRecords()->count()
                    // ])
                    // ->withColumns([
                    //     TextColumn::make('id')
                    //     ])
                    ->withHiddenColumns()
                    ->snappy()
            ])
            ->filters([
                NumberFilter::make('progress'),
                DateFilter::make('start_project'),
                DateFilter::make('end_project'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        // ->visible(function (Model $record) {
                        //     return $record->progress < 100.0;
                        // }),
                    // Tables\Actions\DeleteAction::make()
                    //     ->visible(function (Model $record) {
                    //         return $record->progress < 100.0;
                    //     }),
                ]),
            ]);
            // ->bulkActions([
            //     Tables\Actions\DeleteBulkAction::make(),
            // ]);
    }

    public static function getRelations(): array
    {
        return [
            ProjectPlanDetailsRelationManager::class,
            ProjectCostRelationManager::class,
            //CashFlowsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectPlans::route('/'),
            'create' => Pages\CreateProjectPlan::route('/create'),
            'view' => Pages\ViewProjectPlan::route('/{record}'),
            'edit' => Pages\EditProjectPlan::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() - 3;
    }
}
