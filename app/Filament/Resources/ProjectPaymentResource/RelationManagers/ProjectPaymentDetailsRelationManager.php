<?php

namespace App\Filament\Resources\ProjectPaymenteResource\RelationManagers;

use App\Filament\Common\Common;
use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

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
                Forms\Components\DatePicker::make('transaction_date'),
                Forms\Components\Select::make('category')
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
                Tables\Columns\TextColumn::make('transaction_date')->date(),
                Tables\Columns\TextColumn::make('category'),
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
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
