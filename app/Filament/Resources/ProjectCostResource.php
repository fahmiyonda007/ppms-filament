<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectCostResource\Pages;
use App\Filament\Resources\ProjectCostResource\RelationManagers;
use App\Filament\Resources\ProjectCostResource\RelationManagers\ProjectCostDetailsRelationManager;
use App\Models\CoaThird;
use App\Models\ProjectCost;
use App\Models\ProjectCostDetail;
use App\Models\ProjectPlan;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;
use Webbingbrasil\FilamentAdvancedFilter\Filters\BooleanFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class ProjectCostResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ProjectCost::class;
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?string $slug = 'project/costs';
    protected static ?string $navigationGroup = 'Projects';
    protected static ?string $navigationLabel = 'Costs';
    protected static ?string $recordTitleAttribute = 'transaction_code';
    protected static ?int $navigationSort = 2;

    public $arr = [];

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
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, $state) {
                                    $projectCosts = ProjectCost::where('project_plan_id', $state);
                                    $len = str_pad($projectCosts->count() + 1, 3, '0', STR_PAD_LEFT);
                                    $set('transaction_code', 'TRX-' . $state . '-' . $len);
                                }),
                            Forms\Components\Select::make('vendor_id')
                                ->relationship('vendor', 'name')
                                ->searchable()
                                ->preload()
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
                    Grid::make(3)
                        ->schema([
                            Forms\Components\Select::make('coa_id_source1')
                                ->relationship(
                                    'coaThird1',
                                    'name',
                                    fn (Builder $query) => $query->where('code', 'like', '1%')
                                )
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                                ->searchable()
                                ->preload()
                                ->label('Payment Source')
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set) {
                                    $set('coa_id_source2', null);
                                    $set('coa_id_source3', null);
                                }),
                            Forms\Components\Select::make('coa_id_source2')
                                ->relationship(
                                    'coaThird2',
                                    'name',
                                    function (Builder $query, Closure $get) {
                                        if ($get('coa_id_source1') == null) {
                                            $query->where('id', 0);
                                        } else {
                                            $query
                                                ->where([
                                                    ['code', 'like', '1%'],
                                                    ['id', '!=', $get('coa_id_source1')],
                                                ]);
                                        }
                                    }
                                )
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                                ->searchable()
                                ->preload()
                                ->label('Payment Source')
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set) {
                                    $set('coa_id_source3', null);
                                }),
                            Forms\Components\Select::make('coa_id_source3')
                                ->relationship(
                                    'coaThird3',
                                    'name',
                                    function (Builder $query, Closure $get) {
                                        if ($get('coa_id_source2') == null) {
                                            $query->where('id', 0);
                                        } else {
                                            $query
                                                ->where([
                                                    ['code', 'like', '1%'],
                                                    ['id', '!=', $get('coa_id_source1')],
                                                    ['id', '!=', $get('coa_id_source2')],
                                                ]);
                                        }
                                    }
                                )
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                                ->searchable()
                                ->preload()
                                ->label('Payment Source')
                        ]),

                    Grid::make(1)
                        ->schema([
                            Forms\Components\RichEditor::make('description')
                                ->maxLength(500),
                        ]),
                ]),
                Card::make([
                    Forms\Components\TextInput::make('total_amount')
                        ->disabled()
                        ->numeric()
                        ->mask(
                            fn (Mask $mask) => $mask
                                ->numeric()
                                ->thousandsSeparator(',')
                        ),
                    Forms\Components\TextInput::make('id'),
                    TableRepeater::make('details')
                        ->relationship('projectCostDetails')
                        ->schema([
                            Forms\Components\Select::make('coa_id')
                                ->relationship(
                                    'coaThird',
                                    'name',
                                    function (Builder $query, Closure $get) {
                                        $query->where([
                                            ['code', 'like', '5%'],
                                        ]);
                                    }
                                )
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                                ->required()
                                ->columnSpan(2)
                                ->searchable()
                                ->preload(),
                            Forms\Components\TextInput::make('uom')
                                ->required(),
                            Forms\Components\TextInput::make('qty')
                                ->required()
                                ->numeric(),
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
                            Forms\Components\TextInput::make('amount')
                                ->disabled()
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                        ])
                        ->colStyles([
                            'coa_id' => 'width: 450px;',
                        ])
                        ->createItemButtonLabel('ADD DETAILS')
                        ->collapsible()
                ])->visibleOn('edit')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('projectPlan.name')->sortable(),
                Tables\Columns\TextColumn::make('transaction_code')->sortable(),
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
            // ProjectCostDetailsRelationManager::class,
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

    protected function arrRepeater(): array
    {
        return [
            Repeater::make('projectCostDetails')
                ->relationship()
                ->schema([
                    Grid::make(6)
                        ->schema([
                            Forms\Components\Select::make('coa_id')
                                ->relationship(
                                    'coaThird',
                                    'name',
                                    function (Builder $query, Closure $get) {
                                        $query->where('code', 'like', '5%');
                                    }
                                )
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                                ->required()
                                ->columnSpan(2)
                                ->searchable()
                                ->preload(),
                            Forms\Components\TextInput::make('uom')
                                ->required(),
                            Forms\Components\TextInput::make('qty')
                                ->required()
                                ->numeric()
                                // ->reactive()
                                // ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                                //     $unit_price = $get('unit_price');
                                //     $val = (float)$state * (float)$unit_price;
                                //     $set('amount', (string)$val);
                                // })
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('unit_price')
                                ->required()
                                ->numeric()
                                // ->reactive()
                                // ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                                //     $qty = $get('qty');
                                //     $val = (float)$state * (float)$qty;
                                //     $set('amount', (string)$val);
                                // })
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('amount')
                                ->disabled()
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->thousandsSeparator(',')
                                ),
                        ])
                ])
                ->collapsible()
        ];
    }
}
