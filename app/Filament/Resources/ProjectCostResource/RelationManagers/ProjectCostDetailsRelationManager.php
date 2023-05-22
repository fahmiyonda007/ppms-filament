<?php

namespace App\Filament\Resources\ProjectCostResource\RelationManagers;

use App\Filament\Resources\ProjectCostResource;
use App\Models\ProjectCost;
use App\Models\ProjectCostDetail;
use App\Models\SysLookup;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Client\Request;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Support\Str;


class ProjectCostDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectCostDetails';
    protected static ?string $title = 'Details';
    protected static ?string $label = 'Details';

    public static function form(Form $form): Form
    {
        return $form
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
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->code} - {$record->name}")
                    ->required()
                    ->columnSpanFull()
                    ->unique(column: 'coa_id',
                        ignoreRecord: true,
                        callback: function (Unique $rule, RelationManager $livewire) {
                            return $rule
                                ->where('project_cost_id', $livewire->ownerRecord->id);
                        }),
                Forms\Components\Select::make('uom')
                    ->multiple(false)
                    ->searchable()
                    ->required()
                    ->preload()
                    ->options(SysLookup::where('group_name', 'UOM')->pluck('name', 'name')),
                Forms\Components\TextInput::make('qty')
                    ->required()
                    ->numeric()
                    ->mask(
                        fn(Mask $mask) => $mask
                            ->numeric()
                            ->thousandsSeparator(',')
                    )
                    ->reactive()
                    ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                        $unit_price = $get('unit_price');
                        $val = (float) $state * (float) $unit_price;
                        $set('amount', (string) $val);
                    }),
                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->mask(
                        fn(Mask $mask) => $mask
                            ->numeric()
                            ->decimalPlaces(2)
                            ->decimalSeparator(',')
                            ->thousandsSeparator(',')
                    )
                    ->reactive()
                    ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                        $qty = $get('qty');
                        $val = (float) $state * (float) $qty;
                        $set('amount', (string) $val);
                    }),
                Forms\Components\TextInput::make('amount')
                    ->disabled()
                    ->numeric()
                    ->mask(
                        fn(Mask $mask) => $mask
                            ->numeric()
                            ->decimalPlaces(2)
                            ->decimalSeparator(',')
                            ->thousandsSeparator(',')
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('coaThird.fullname')->label('COA'),
                Tables\Columns\TextColumn::make('uom'),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('unit_price')->money('idr', true),
                Tables\Columns\TextColumn::make('amount')->money('idr', true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        return $isEdit && $livewire->ownerRecord->payment_status == "NOT PAID";
                    })
                    ->using(function (HasRelationshipTable $livewire, array $data): Model {
                        $data['created_by'] = auth()->user()->email;
                        return $livewire->getRelationship()->create($data);
                    })
                    ->after(function (RelationManager $livewire, Model $record) {
                        $details = ProjectCostDetail::where('project_cost_id', $record->project_cost_id);
                        $header = ProjectCost::find($record->project_cost_id);
                        $header->total_amount = $details->sum('amount');
                        $header->save();
                        $livewire->emit('refresh');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        return $isEdit && $livewire->ownerRecord->payment_status == "NOT PAID";
                    })
                    ->using(function (HasRelationshipTable $livewire, Model $record, array $data): Model {
                        $data['updated_by'] = auth()->user()->email;
                        $record->update($data);
                        return $record;
                    })
                    ->after(function (RelationManager $livewire, Model $record) {
                        $details = ProjectCostDetail::where('project_cost_id', $record->project_cost_id);
                        $header = ProjectCost::find($record->project_cost_id);
                        $header->total_amount = $details->sum('amount');
                        $header->save();
                        $livewire->emit('refresh');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        return $isEdit && $livewire->ownerRecord->payment_status == "NOT PAID";
                    })
                    ->after(function (RelationManager $livewire, Model $record) {
                        $details = ProjectCostDetail::where('project_cost_id', $record->project_cost_id);
                        $header = ProjectCost::find($record->project_cost_id);
                        $header->total_amount = $details->sum('amount');
                        if ((float) $details->sum('amount') == 0) {
                            $header->coa_id_source1 = null;
                            $header->coa_id_source2 = null;
                            $header->coa_id_source3 = null;
                        }
                        $header->save();
                        $livewire->emit('refresh');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        return $isEdit && $livewire->ownerRecord->payment_status == "NOT PAID";
                    }),
            ]);
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 20];
    }
}