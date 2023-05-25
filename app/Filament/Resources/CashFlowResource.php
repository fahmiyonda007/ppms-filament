<?php

namespace App\Filament\Resources;

use App\Filament\Common\Common;
use App\Filament\Resources\CashFlowResource\Pages;
use App\Filament\Resources\CashFlowResource\RelationManagers;
use App\Filament\Resources\CashFlowResource\RelationManagers\CashFlowDetailsRelationManager;
use App\Models\CashFlow;
use App\Models\CoaThird;
use App\Models\ProjectPlan;
use App\Models\SysLookup;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Illuminate\Support\Str;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class CashFlowResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CashFlow::class;
    protected static ?string $navigationIcon = 'heroicon-o-refresh';
    protected static ?string $slug = 'cash/cash-flow';
    protected static ?string $navigationGroup = 'Cash';
    protected static ?string $navigationLabel = 'Cash Flows';
    // protected static ?string $recordTitleAttribute = 'transaction_code';
    // protected static ?int $navigationSort = 3;

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
                                ->maxLength(20)
                                ->required()
                                ->disabled()
                                ->default(fn () => Common::getNewCashFlowTransactionId()),
                            Forms\Components\DatePicker::make('transaction_date')
                                ->required(),
                            Forms\Components\Radio::make('cash_flow_type')
                                ->options([
                                    'SETOR_MODAL' => 'SETOR MODAL',
                                    'CASH_OUT' => 'CASH OUT',
                                ])
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set) {
                                    $set('coa_id', null);
                                })
                                ->default('CASH_OUT')
                                ->inline()
                                ->columnSpanFull(),
                            Forms\Components\Select::make('coa_id')
                                ->required()
                                ->label('COA')
                                ->preload()
                                ->reactive()
                                ->searchable()
                                ->options(function (callable $get) {
                                    if ($get('cash_flow_type') == 'SETOR_MODAL') {
                                        $datas = Common::getViewCoaMasterDetails([
                                            ["level_first_id", "=", 3],
                                            ["level_second_code", "=", "01"],
                                        ])->get();
                                    } else if ($get('cash_flow_type') == 'CASH_OUT') {
                                        $datas = Common::getViewCoaMasterDetails([
                                            ["level_first_id", "=", 1],
                                            ["balance", ">", 0],
                                            ["level_second_code", "=", "01"],
                                        ])->get();
                                    }
                                    return $datas->pluck('level_third_name', 'level_third_id');
                                }),
                            Forms\Components\Select::make('project_plan_id')
                                ->options(function () {
                                    $main = ProjectPlan::all()->pluck('name', 'id')->toArray();
                                    //$add = SysLookup::where('group_name', 'ADD PROJECT')->get()->pluck('name', 'name')->toArray();
                                    //$datas = array_merge($add, $main);
                                    return $main;
                                })
                                ->preload()
                                ->searchable(),
                        ]),
                    Placeholder::make('coa_balance')
                        ->label('COA Balance')
                        ->content(function (callable $get) {
                            $coa = CoaThird::find($get('coa_id'));
                            $num = $coa->balance ?? 0;
                            $cond = Str::startsWith($coa->code ?? 0, '301') && !auth()->user()->hasRole(['super_admin', 'admin']);
                            if ($cond == true) {
                                return 'Rp XXX.XXX.XXX.XXX';
                            } else {
                                return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                            }
                        })
                        ->hidden(function ($record) {
                            if ($record) {
                                if ($record->is_jurnal == 1) {
                                    return true;
                                }
                                return false;
                            }
                        }),
                    Forms\Components\Textarea::make('description')
                        ->maxLength(500),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_jurnal')->label('Post Journal')->boolean(),
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
                        'SETOR_MODAL' => 'SETOR MODAL',
                        'CASH_OUT' => 'CASH OUT',
                    ]),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                TextFilter::make('transaction_code'),
                DateFilter::make('transaction_date'),
                SelectFilter::make('cash_flow_type')
                    ->options([
                        'SETOR_MODAL' => 'SETOR MODAL',
                        'CASH_OUT' => 'CASH OUT',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(function ($record) {
                            if ($record) {
                                if ($record->is_jurnal == 1) {
                                    return false;
                                }
                                return true;
                            }
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->visible(function ($record) {
                            if ($record) {
                                if ($record->is_jurnal == 1) {
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
            CashFlowDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashFlows::route('/'),
            'create' => Pages\CreateCashFlow::route('/create'),
            'view' => Pages\ViewCashFlow::route('/{record}'),
            'edit' => Pages\EditCashFlow::route('/{record}/edit'),
        ];
    }
}
