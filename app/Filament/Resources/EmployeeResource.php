<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\SysLookup;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;

class EmployeeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Employee::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $slug = 'master/employess';
    protected static ?string $navigationGroup = 'Masters';
    protected static ?string $navigationLabel = 'Employees';
    protected static ?string $recordTitleAttribute = 'employee_name';
    // protected static ?int $navigationSort = 2;


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
                    Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('nik')
                                ->label('NIK')
                                ->required()
                                ->maxLength(15)
                                ->unique(ignoreRecord: true)
                                ->numeric(),
                            Forms\Components\TextInput::make('ktp')
                                ->label('KTP')
                                ->maxLength(16)
                                ->numeric()
                                ->unique(ignoreRecord: true)
                                ->required(),
                            Forms\Components\Select::make('department')
                                ->multiple(false)
                                ->searchable()
                                ->required()
                                ->preload()
                                ->options(SysLookup::where('group_name', 'DEPARTMENT')->pluck('name', 'name')),
                            Forms\Components\TextInput::make('employee_name')
                                ->label('Name')
                                ->required()
                                ->maxLength(50),
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('phone')
                                ->tel()
                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                ->maxLength(13),
                            Forms\Components\Textarea::make('address')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('salary_day')
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('overtime')
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\TextInput::make('total_loan')
                                ->numeric()
                                ->mask(
                                    fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->thousandsSeparator(',')
                                ),
                            Forms\Components\Select::make('bank_account_id')
                                ->relationship('bankAccount', 'account_name')
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->account_number} - {$record->account_name}")
                                ->searchable()
                                ->preload(),
                            Forms\Components\DatePicker::make('join_date'),
                            Forms\Components\DateTimePicker::make('resign_date'),
                            Forms\Components\Toggle::make('is_resign'),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')->label('NIK')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ktp')->label('KTP')->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee_name')->label('Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('join_date')
                    ->date(),
                Tables\Columns\TextColumn::make('salary_day')->money('idr', true),
                Tables\Columns\TextColumn::make('overtime')->money('idr', true),
                Tables\Columns\TextColumn::make('total_loan')->money('idr', true),
                Tables\Columns\TextColumn::make('bankAccount.bank_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bankAccount.account_name'),
                Tables\Columns\TextColumn::make('bankAccount.account_number'),
                Tables\Columns\ToggleColumn::make('is_resign'),
                Tables\Columns\TextColumn::make('resign_date')
                    ->date(),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->searchable()
                    ->options(SysLookup::where('group_name', 'DEPARTMENT')->pluck('name', 'name')),
                DateFilter::make('join_date'),
                DateFilter::make('resign_date'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
