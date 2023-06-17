<?php

namespace App\Filament\Resources\CashFlowResource\RelationManagers;

use App\Filament\Common\Common;
use App\Filament\Resources\CashFlowResource;
use App\Models\CoaThird;
use Awcodes\Shout\Shout;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;
use Illuminate\Support\Str;

class CashFlowDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'cashFlowDetails';
    // protected static ?string $recordTitleAttribute = 'cash_flow_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        Forms\Components\Select::make('coa_id')
                            ->required()
                            ->label('COA')
                            ->preload()
                            ->searchable()
                            ->options(function (RelationManager $livewire) {
                                $type = $livewire->ownerRecord->cash_flow_type;
                                if ($type == 'SETOR_MODAL') {
                                    $datas = Common::getViewCoaMasterDetails([
                                        ["level_first_id", "=", 1],
                                        ["level_second_code", "=", "01"],
                                    ])->get();
                                } else if ($type == 'CASH_OUT') {
                                    $datas = Common::getViewCoaMasterDetails([
                                        ["level_first_id", "=", 5],
                                    ])->get();
                                }
                                return $datas->pluck('level_third_name', 'level_third_id');
                            }),
                        // Card::make([
                        //     // Placeholder::make('coa_detail_balance')
                        //     //     ->label('COA Detail Balance')
                        //     //     ->content(function (callable $get) {
                        //     //         $num = CoaThird::find($get('coa_id'))->balance ?? 0;
                        //     //         return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                        //     //     }),
                        //     Placeholder::make('coa_header_balance')
                        //         ->label('COA Header Balance')
                        //         ->content(function (callable $get, RelationManager $livewire) {
                        //             $coaId = $livewire->ownerRecord->coa_id;
                        //             $coa = CoaThird::find($coaId);
                        //             $num = $coa->balance ?? 0;
                        //             $cond = Str::startsWith($coa->code ?? 0, '301') && !auth()->user()->hasRole(['super_admin', 'admin']);
                        //             if ($cond == true) {
                        //                 return 'Rp XXX.XXX.XXX.XXX';
                        //             } else {
                        //                 return 'Rp ' . number_format($num ?? 0, 0, ',', '.');
                        //             }
                        //         }),
                        // ])
                        //     ->columns(2)
                        //     ->hidden(function (RelationManager $livewire) {
                        //         $header = $livewire->ownerRecord;
                        //         if ($header->is_jurnal == 1) {
                        //             return true;
                        //         }
                        //         return false;
                        //     }),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->mask(
                                fn (Mask $mask) => $mask
                                    ->numeric()
                                    ->decimalPlaces(2)
                                    ->decimalSeparator(',')
                                    ->thousandsSeparator(',')
                            )
                            ->columnSpanFull(),
                        // Shout::make('error')
                        //     ->content('COA Header Balance kurang.')
                        //     ->type('danger')
                        //     ->columnSpan('full')
                        //     ->hidden(function (callable $get, RelationManager $livewire) {
                        //         $header = $livewire->ownerRecord;
                        //         if ($header->cash_flow_type == 'CASH_OUT') {
                        //             $coaThird = CoaThird::find($header->coa_id)->balance ?? 0;
                        //             $num = (float)$coaThird - (float)$get('amount');
                        //             return $num >= 0;
                        //         }
                        //         return true;
                        //     }),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('coaThird.fullname')
                    ->label('COA')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name']),
                Tables\Columns\TextColumn::make('amount')->money('idr', true),
                Tables\Columns\TextColumn::make('description')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->afterFormValidated(function (CreateAction $action, RelationManager $livewire, array $data) {
                        $header = $livewire->ownerRecord;
                        if ($header->cash_flow_type == 'CASH_OUT') {
                            $coaThird = CoaThird::find($header->coa_id)->balance ?? 0;
                            $num = (float)$coaThird - (float)$data['amount'];
                            if ($num < 0) {
                                $action->halt();
                            }
                        }
                    })
                    ->after(function (RelationManager $livewire) {
                        redirect(CashFlowResource::getUrl('view', ['record' => $livewire->ownerRecord]));
                    })
                    ->visible(function (RelationManager $livewire) {
                        $header = $livewire->ownerRecord;
                        if ($header->is_jurnal) {
                            return false;
                        }
                        return true;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $header = $livewire->ownerRecord;
                        if ($header->is_jurnal) {
                            return false;
                        }
                        return true;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function (RelationManager $livewire) {
                        redirect(CashFlowResource::getUrl('edit', ['record' => $livewire->ownerRecord]));
                    })
                    ->visible(function (RelationManager $livewire) {
                        $header = $livewire->ownerRecord;
                        if ($header->is_jurnal) {
                            return false;
                        }
                        return true;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->after(function (RelationManager $livewire) {
                        redirect(CashFlowResource::getUrl('edit', ['record' => $livewire->ownerRecord]));
                    })
                    ->visible(function (RelationManager $livewire) {
                        $header = $livewire->ownerRecord;
                        if ($header->is_jurnal) {
                            return false;
                        }
                        return true;
                    }),
            ]);
    }
}
