<?php

namespace App\Filament\Resources\ProjectPaymenteResource\RelationManagers;

use App\Filament\Common\Common;
use App\Filament\Resources\Common\JournalRepository;
use App\Filament\Resources\ProjectPaymentResource;
use App\Models\ProjectPayment;
use App\Models\ProjectPaymentDetail;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Position;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ProjectPaymentDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectPaymentDetails';
    protected static ?string $title = 'Detail';
    protected static ?string $label = 'Detail';
    // protected static ?string $recordTitleAttribute = 'project_payment_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('transaction_code')
                    ->maxLength(20)
                    ->required()
                    ->disabled()
                    ->columnSpanFull()
                    ->default(fn () => Common::getNewProjectPaymentTransactionId()),
                Forms\Components\DatePicker::make('transaction_date')
                    ->required()
                    ->default(Carbon::now()),
                Forms\Components\Select::make('category')
                    ->required()
                    ->searchable()
                    ->options([
                        "DP" => "DP",
                        "BOOKING_FEE" => 'BOOKING FEE',
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
                            ["level_first_id", "=", 4],
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
                                ["level_first_id", "=", 1],
                                ["level_second_code", "=", "01"],
                            ])->get();
                        }
                        return $datas->pluck('level_third_name', 'level_third_id');
                    }),
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
                        "BOOKING_FEE" => 'BOOKING FEE',
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

            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    // ->visible(function (RelationManager $livewire) {
                    //     dd($livewire->getRelationship()->get());
                    //     $header = $livewire->ownerRecord;
                    //     $isEdit = Str::contains($livewire->pageClass, '\Edit');
                    //     return $header->is_jurnal == 0;
                    // })
                    ->using(function (RelationManager $livewire, array $data): Model {
                        $header = $livewire->ownerRecord;
                        $lastInc = ProjectPaymentDetail::where([
                            ['project_payment_id', '=', $header->id],
                            ['category', '=', $data['category']],
                        ])->max('inc') + 1;
                        $data['inc'] = $lastInc;
                        return $livewire->getRelationship()->create($data);
                    })
                    ->after(fn ($livewire) => redirect(ProjectPaymentResource::getUrl('edit', ['record' => $livewire->ownerRecord]))),
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
                        ->after(fn ($livewire) => redirect(ProjectPaymentResource::getUrl('edit', ['record' => $livewire->ownerRecord]))),
                ]),
                Tables\Actions\Action::make('post_jurnal')
                    ->label('Post Journal')
                    ->icon('heroicon-s-cash')
                    ->action(fn ($record, $livewire) => static::postJournal($record, $livewire->ownerRecord))
                    ->requiresConfirmation()
                    ->visible(function (Model $record) {
                        return $record->is_jurnal == 0;
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make()
                //     ->visible(function (Model $record) {
                //         return $record->is_jurnal == 0;
                //     }),
            ]);
    }

    protected function getTableActionsPosition(): ?string
    {
        return Position::BeforeCells;
    }

    protected static function postJournal($record, $header)
    {
        JournalRepository::ProjectPaymentDetailPostJournal($record, $header);
        redirect(ProjectPaymentResource::getUrl('edit', ['record' => $header]));
    }
}
