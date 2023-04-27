<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectCostResource\Pages;
use App\Filament\Resources\ProjectCostResource\RelationManagers\ProjectCostDetailsRelationManager;
use App\Models\CoaThird;
use App\Models\ProjectCost;
use App\Models\Vendor;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;
use Illuminate\Support\Str;

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
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make([
                Grid::make(2)->schema([
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
                Grid::make(2)->schema([
                    Forms\Components\Select::make('project_plan_id')
                        ->relationship('projectPlan', 'name')
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->preload()
                        ->afterStateUpdated(function (callable $get, Closure $set, $record, $state) {
                            if ($record) {
                                if ($record->transaction_code == null || $state != $record->project_plan_id) {
                                    $code = static::generateCode($state);
                                    $set('transaction_code', $code);
                                } else {
                                    $set('transaction_code', $record->transaction_code);
                                }
                            } else {
                                $code = static::generateCode($state);
                                $set('transaction_code', $code);
                            }
                        }),
                    Forms\Components\Select::make('vendor_id')
                        ->relationship('vendor', 'name')
                        ->searchable()
                        ->reactive()
                        ->preload()
                        ->required(),
                    Forms\Components\DatePicker::make('order_date')->required(),
                    Forms\Components\DatePicker::make('payment_date')
                        ->disabled(function ($record) {
                            if ($record) {
                                return $record->total_amount == 0;
                            }
                            return false;
                        })
                        ->required(function (callable $get, ?Model $record) {
                            if ($record) {
                                $payment = static::getSumPaymentSource($get, $record);
                                $detail = (float) $record->total_amount;
                                return $detail > 0 && $payment >= $detail;
                            }
                            return false;
                        })
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, callable $get, ?Model $record, $state) {
                            if ($record) {
                                $payment = static::getSumPaymentSource($get, $record);
                                $detail = (float) $record->total_amount;
                                if ($state && $payment >= $detail && $detail > 0) {
                                    $set('payment_status', 'PAID');
                                }
                            } else {
                                $set('payment_status', 'NOT PAID');
                            }
                        }),
                ]),
                Grid::make(1)->schema([Textarea::make('description')->maxLength(500)]),
                Grid::make(3)->schema([
                    Forms\Components\Select::make('coa_id_source1')
                        ->relationship('coaThird1', 'name', function (Builder $query, ?Model $record) {
                            if ($record) {
                                if ((float) $record->total_amount == 0) {
                                    $query->where('id', 0);
                                } else {
                                    $query->where('code', 'like', '1%');
                                }
                            } else {
                                $query->where('id', 0);
                            }
                        })
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                        ->searchable()
                        ->preload()
                        ->label('Payment Source 1')
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set) {
                            $set('coa_id_source2', null);
                            $set('coa_id_source3', null);
                        }),
                    Forms\Components\Select::make('coa_id_source2')
                        ->relationship('coaThird2', 'name', function (Builder $query, Closure $get) {
                            if ($get('coa_id_source1') == null) {
                                $query->where('id', 0);
                            } else {
                                $query->where([['code', 'like', '1%'], ['id', '!=', $get('coa_id_source1')]]);
                            }
                        })
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                        ->searchable()
                        ->preload()
                        ->label('Payment Source 2')
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set) {
                            $set('coa_id_source3', null);
                        }),
                    Forms\Components\Select::make('coa_id_source3')
                        ->relationship('coaThird3', 'name', function (Builder $query, Closure $get) {
                            if ($get('coa_id_source2') == null) {
                                $query->where('id', 0);
                            } else {
                                $query->where([['code', 'like', '1%'], ['id', '!=', $get('coa_id_source1')], ['id', '!=', $get('coa_id_source2')]]);
                            }
                        })
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->label('Payment Source 3'),
                ]),
                Grid::make(3)
                    ->schema([
                        Placeholder::make('amount_source1')
                            ->label('')
                            ->content(function (callable $get) {
                                $res = 0;
                                $coaThird = CoaThird::find($get('coa_id_source1'));
                                if ($coaThird) {
                                    if ($coaThird->name == 'DEPOSIT TOKO' && $get('vendor_id') != null) {
                                        $vendor = Vendor::find($get('vendor_id'));
                                        $res = $vendor->deposit;
                                    } elseif ($coaThird->name != 'DEPOSIT TOKO') {
                                        $res = $coaThird->balance;
                                    }
                                }
                                return 'Rp ' . number_format($res, 0, ',', '.');
                            }),
                        Placeholder::make('amount_source2')
                            ->label('')
                            ->when(function (callable $get) {
                                $coaThird = CoaThird::find($get('coa_id_source2'));
                                return $coaThird !== null;
                            })
                            ->content(function (callable $get) {
                                $coaThird = CoaThird::find($get('coa_id_source2'));
                                return 'Rp ' . number_format($coaThird->balance, 0, ',', '.');
                            }),
                        Placeholder::make('amount_source3')
                            ->label('')
                            ->when(function (callable $get) {
                                $coaThird = CoaThird::find($get('coa_id_source3'));
                                return $coaThird !== null;
                            })
                            ->content(function (callable $get) {
                                $coaThird = CoaThird::find($get('coa_id_source3'));
                                return 'Rp ' . number_format($coaThird->balance, 0, ',', '.');
                            }),
                    ])
                    ->visible(function ($record) {
                        if ($record) {
                            return $record->payment_status == 'NOT PAID';
                        }
                        return true;
                    }),
            ]),
            Card::make([
                Placeholder::make('total_payment_source')
                    ->label('Total Payment Source')
                    ->content(function (callable $get, Closure $set, ?Model $record) {
                        $payment = static::getSumPaymentSource($get, $record);
                        $detail = (float) $record->total_amount;
                        if ($get('payment_date') !== null && $payment >= $detail && $detail > 0) {
                            $set('payment_status', 'PAID');
                        } else {
                            $set('payment_status', 'NOT PAID');
                        }
                        return 'Rp ' . number_format($payment, 2, ',', '.');
                    }),
                Placeholder::make('total_amount_detail')
                    ->label('Total Amount Detail')
                    ->content(function (Model $record) {
                        return 'Rp ' . number_format($record->total_amount, 2, ',', '.');
                    }),
                AlertBox::make()
                    ->label(label: 'Oops...')
                    ->helperText(text: 'Pembayaran kurang dari Total Amount.')
                    ->resolveIconUsing(name: 'heroicon-o-x-circle')
                    ->warning()
                    ->hidden(function (?Model $record, callable $get) {
                        $payment = static::getSumPaymentSource($get, $record);
                        $detail = (float) $record->total_amount;
                        return $detail <= $payment;
                    })
                    ->columnSpanFull(),
            ])
                ->columns(2)
                ->visibleOn(['view', 'edit']),
            Card::make([
                // FileUpload::make('attachment')
                //     // ->disk('s3')
                //     ->directory(fn ($record) => 'cost-attachments\\' . $record->transaction_code)
                //     // ->visibility('private')
                //     ->multiple()
                //     ->image()
                //     ->imageResizeMode('cover')
                //     ->imageCropAspectRatio('16:9')
                //     ->imageResizeTargetWidth('1920')
                //     ->imageResizeTargetHeight('1080')
                //     ->imagePreviewHeight('250')
                //     ->enableReordering()
                //     ->enableOpen()
                //     ->enableDownload()
                //     ->imagePreviewHeight('250')
                //     ->maxSize(1024)
                //     ->storeFileNamesIn('attachment_file_names')
                SpatieMediaLibraryFileUpload::make('attachment')
                    ->multiple()
                    ->responsiveImages()
                    ->disk('public')
                    ->enableOpen()
                    ->enableDownload()
                    ->image()
                    ->maxSize(1024)
                    ->collection(fn ($record) => 'project-costs\\' . $record->transaction_code)
                    // ->collection('project-costs')
                    ->imagePreviewHeight(100)
                    // ->panelAspectRatio('2:1')
                    // ->imageCropAspectRatio('2:1')
                    // ->panelLayout('integrated')
                    ->required(function (callable $get, ?Model $record) {
                        if ($record) {
                            $payment = static::getSumPaymentSource($get, $record);
                            $detail = (float) $record->total_amount;
                            return $detail > 0 && $payment >= $detail;
                        }
                        return false;
                    })
                    ->enableReordering(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('projectPlan.name')
                    ->sortable()
                    ->searchable(['project_plans.name']),
                Tables\Columns\TextColumn::make('transaction_code')
                    ->sortable()
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('total_payment')
                    ->money('idr', true)
                    ->sortable()
                    ->color(fn ($record) => $record->total_amount > $record->total_payment ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('idr', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('coaThird1.fullname')
                    ->label('Payment Source 1')
                    ->sortable(['name']),
                Tables\Columns\TextColumn::make('coaThird2.fullname')
                    ->label('Payment Source 2')
                    ->sortable(['name']),
                Tables\Columns\TextColumn::make('coaThird3.fullname')
                    ->label('Payment Source 3')
                    ->sortable(['name']),
            ])
            ->filters([TextFilter::make('payment_status'), SelectFilter::make('projectPlan.name'), SelectFilter::make('vendor.name'), TextFilter::make('transaction_code'), DateFilter::make('payment_date'), DateFilter::make('order_date')])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->visible(function (Model $record) {
                        return $record->payment_status == 'NOT PAID';
                    }),
                    Tables\Actions\DeleteAction::make()->visible(function (Model $record) {
                        return $record->payment_status == 'NOT PAID';
                    }),
                ]),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getRelations(): array
    {
        return [ProjectCostDetailsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectCosts::route('/'),
            'create' => Pages\CreateProjectCost::route('/create'),
            'view' => Pages\ViewProjectCost::route('/{record}'),
            'edit' => Pages\EditProjectCost::route('/{record}/edit'),
        ];
    }

    protected static function getSumPaymentSource(callable $get, Model $record): float
    {
        $coaThird1 = 0;
        $coaThird = CoaThird::find($get('coa_id_source1'));
        if ($coaThird) {
            $cond = $coaThird->name == 'DEPOSIT TOKO' && $get('vendor_id') != null;
            if ($cond) {
                $vendor = Vendor::find($get('vendor_id'));
                $coaThird1 = $vendor->deposit;
            } else {
                $coaThird1 = $coaThird->balance;
            }
        }
        $coaThird2 = CoaThird::find($get('coa_id_source2'))->balance ?? 0;
        $coaThird3 = CoaThird::find($get('coa_id_source3'))->balance ?? 0;
        $sum = (float) $coaThird1 + (float) $coaThird2 + (float) $coaThird3;
        $res = (float) $record->total_payment != null && (float) $record->total_payment == $sum ? $record->total_payment : $sum;

        return $res;
    }

    protected function arrRepeater(): array
    {
        return [
            Repeater::make('projectCostDetails')
                ->relationship()
                ->schema([
                    Grid::make(6)->schema([
                        Forms\Components\Select::make('coa_id')
                            ->relationship('coaThird', 'name', function (Builder $query, Closure $get) {
                                $query->where('code', 'like', '5%');
                            })
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->code} - {$record->name}")
                            ->required()
                            ->columnSpan(2)
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('uom')->required(),
                        Forms\Components\TextInput::make('qty')
                            ->required()
                            ->numeric()
                            // ->reactive()
                            // ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                            //     $unit_price = $get('unit_price');
                            //     $val = (float)$state * (float)$unit_price;
                            //     $set('amount', (string)$val);
                            // })
                            ->mask(fn (Mask $mask) => $mask->numeric()->thousandsSeparator(',')),
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
                                    ->thousandsSeparator(','),
                            ),
                        Forms\Components\TextInput::make('amount')
                            ->disabled()
                            ->numeric()
                            ->mask(
                                fn (Mask $mask) => $mask
                                    ->numeric()
                                    ->decimalPlaces(2)
                                    ->decimalSeparator(',')
                                    ->thousandsSeparator(','),
                            ),
                    ]),
                ])
                ->collapsible(),
        ];
    }

    protected static function generateCode($state): string
    {
        $projectCosts = ProjectCost::where('project_plan_id', $state)->max('transaction_code');
        $lastCode = $projectCosts ?? 'TRX-' . $state . '-000';
        $num = (int)Str::substr($lastCode, Str::length($lastCode) - 3, 3) + 1;
        $len = str_pad($num, 3, '0', STR_PAD_LEFT);
        return 'TRX-' . $state . '-' . $len;
    }
}
