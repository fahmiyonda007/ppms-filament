<?php

namespace App\Filament\Resources\VendorLiabilityResource\RelationManagers;

use App\Filament\Common\Common;
use App\Filament\Resources\Common\JournalRepository;
use App\Filament\Resources\VendorLiabilityResource;
use App\Models\VendorLiability;
use App\Models\VendorLiabilityPayment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\Position;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use KoalaFacade\FilamentAlertBox\Forms\Components\AlertBox;

class VendorLiabilityPaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'vendorLiabilityPayments';
    protected static ?string $title = 'Detail';
    protected static ?string $label = 'Detail';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('transaction_code')
                    ->maxLength(20)
                    ->required()
                    ->disabled()
                    ->columnSpanFull()
                    ->default(fn () => Common::getNewVendorLiabilityPaymentTransactionId()),
                Forms\Components\DatePicker::make('transaction_date')
                    ->required()
                    ->default(Carbon::now()),
                Forms\Components\Select::make('category')
                    ->required()
                    ->searchable()
                    ->options([
                        "DP" => "DP",
                        "PAYMENT" => 'PAYMENT',
                    ]),
                Forms\Components\Select::make('coa_id_source')
                    ->label('Source')
                    ->required()
                    ->reactive()
                    ->preload()
                    ->searchable()
                    ->options(function () {
                        $datas = Common::getViewCoaMasterDetails([
                            ["level_first_id", "=", 1],
                            ["balance", ">", 0],
                            ["level_second_code", "=", "01"],
                        ])->get();
                        return $datas->pluck('level_third_name', 'level_third_id');
                    }),
                Forms\Components\Select::make('coa_id_destination')
                    ->label('Destination')
                    ->required()
                    ->preload()
                    ->reactive()
                    ->searchable()
                    ->options(function (callable $get) {
                        $datas = new Collection();
                        if ($get('coa_id_source') != null) {
                            $datas = Common::getViewCoaMasterDetails([
                                ["level_first_id", "=", 5],
                                ["level_second_code", "=", "01"],
                            ])->get();
                        }
                        return $datas->pluck('level_third_name', 'level_third_id');
                    }),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->reactive()
                    ->required()
                    ->mask(
                        fn (Mask $mask) => $mask
                            ->numeric()
                            ->decimalPlaces(2)
                            ->decimalSeparator(',')
                            ->thousandsSeparator(',')
                    )
                    ->columnSpanFull(),
                AlertBox::make()
                    ->label(label: 'ERROR')
                    ->helperText(text: 'Amount tidak boleh melebihi outstanding.')
                    ->resolveIconUsing(name: 'heroicon-o-x-circle')
                    ->danger()
                    ->visible(function ($livewire, $get) {
                        $outstanding = (float)$livewire->ownerRecord->outstanding - (float)$get('amount');
                        return $outstanding < 0;
                    })
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_jurnal')->label('Post Journal')->boolean(),
                Tables\Columns\TextColumn::make('transaction_code'),
                Tables\Columns\TextColumn::make('transaction_date')->date(),
                Tables\Columns\TextColumn::make('category')
                    ->enum([
                        "DP" => "DP",
                        "PAYMENT" => 'PAYMENT',
                    ]),
                Tables\Columns\TextColumn::make('coaThirdSource.fullname')
                    ->label('COA Source')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: true),
                Tables\Columns\TextColumn::make('coaThirdDestination.fullname')
                    ->label('COA Destination')
                    ->sortable(['name'])
                    ->searchable(['coa_level_thirds.name'], isIndividual: true),
                Tables\Columns\TextColumn::make('amount')->money('idr', true),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(function ($livewire) {
                        $outstanding = (float)$livewire->ownerRecord->outstanding;
                        return $outstanding > 0;
                    })
                    ->using(function (RelationManager $livewire, array $data, CreateAction $action): Model {
                        $header = $livewire->ownerRecord;
                        $lastInc = VendorLiabilityPayment::where([
                            ['vendor_liabilities_id', '=', $header->id],
                            ['category', '=', $data['category']],
                        ])->max('inc') + 1;
                        $data['inc'] = $lastInc;

                        $outstanding = (float)$header->outstanding - (float)$data['amount'];

                        if ($outstanding < 0) {
                            Notification::make()
                                ->title('Amount melebihi outstanding')
                                ->danger()
                                ->send();
                            $action->halt();
                        }

                        $header->update([
                            'outstanding' => $outstanding
                        ]);

                        return $livewire->getRelationship()->create($data);
                    })
                    ->after(fn ($livewire) => redirect(VendorLiabilityResource::getUrl('edit', ['record' => $livewire->ownerRecord]))),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->visible(function (Model $record) {
                            return $record->is_jurnal == 0;
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->visible(function (Model $record) {
                            return $record->is_jurnal == 0;
                        })
                        ->after(function ($livewire) {
                            $header = $livewire->ownerRecord;
                            $outstanding = (float)$header->deal_price - (float)$livewire->getRelationship()->sum('amount');
                            $header->update([
                                'outstanding' => $outstanding
                            ]);
                            redirect(VendorLiabilityResource::getUrl('edit', ['record' => $livewire->ownerRecord]));
                        })
                ]),
                Tables\Actions\Action::make('post_jurnal')
                    ->button()
                    ->label('Post Journal')
                    ->icon('heroicon-s-cash')
                    ->action(fn ($record, $livewire) => static::postJournal($record, $livewire->ownerRecord))
                    ->requiresConfirmation()
                    ->visible(function (Model $record) {
                        return $record->is_jurnal == 0;
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected function getTableActionsPosition(): ?string
    {
        return Position::BeforeCells;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 20];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->fastPaginate($this->getTableRecordsPerPage());
    }

    protected static function postJournal($record, $header)
    {
        JournalRepository::VendorLiabilityPaymentPostJournal($record, $header);
        redirect(VendorLiabilityResource::getUrl('edit', ['record' => $header]));
    }
}
