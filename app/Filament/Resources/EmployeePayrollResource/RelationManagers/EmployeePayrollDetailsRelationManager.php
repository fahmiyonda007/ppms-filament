<?php

namespace App\Filament\Resources\EmployeePayrollResource\RelationManagers;

use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollDetail;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use illuminate\Support\Str;

class EmployeePayrollDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'employeePayrollDetails';
    // protected static ?string $recordTitleAttribute = 'payroll_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'employee_name', function (Builder $query, RelationManager $livewire, ?Model $record) {
                                $details =  $livewire->ownerRecord->EmployeePayrollDetails;
                                if ($record) {
                                    $details =  $livewire->ownerRecord->EmployeePayrollDetails->where('id', '!=', $record->id);
                                }

                                $excludeEmployee = collect();
                                foreach ($details as $value) {
                                    $excludeEmployee->push($value->employee_id);
                                }
                                $query->whereNotIn('id', $excludeEmployee);
                            })
                            ->preload()
                            ->searchable()
                            ->unique(column: 'employee_id', ignoreRecord: true, callback: function (Unique $rule, RelationManager $livewire) {
                                return $rule
                                    ->where('payroll_id', $livewire->ownerRecord->id);
                            })
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $employee = Employee::find($state);
                                $set('salary_type', $employee->salary_type ?? '');
                                $set('unit_price', $employee->salary_amount ?? '');
                                $set('overtime', $employee->overtime ?? '');
                                $set('total_loan', $employee->total_loan ?? '');
                                $set('support_price', $employee->support_price ?? '');
                                $set('cor_price', $employee->cor_price ?? '');
                                $set('outstanding', $employee->total_loan ?? '0');
                                if ($employee->salary_type ?? '' == 'MONTHLY') {
                                    $set('total_days', 1);
                                    $set('start_date', new Carbon('first day of this month'));
                                    $set('end_date', new Carbon('last day of this month'));
                                } else {
                                    $set('total_days', '');
                                }
                            }),
                        Forms\Components\TextInput::make('salary_type')
                            ->disabled(),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->reactive()
                                    ->required()
                                    ->default(Carbon::now()->startOfMonth())
                                    ->maxDate(function (callable $get) {
                                        if ($get('end_date')) {
                                            return new Carbon($get('end_date'));
                                        }
                                    })
                                    ->afterStateUpdated(function (Closure $set, callable $get, $state) {
                                        $start = Carbon::parse($state);
                                        $end = Carbon::parse($get('end_date'));
                                        $diff = $start->diffInDays($end);
                                        if ($get('salary_type') == 'DAILY') {
                                            $set('total_days', $diff);
                                        }
                                    }),
                                Forms\Components\DatePicker::make('end_date')
                                    ->reactive()
                                    ->required()
                                    ->default(Carbon::now()->endOfMonth())
                                    ->minDate(function (callable $get) {
                                        if ($get('start_date')) {
                                            return new Carbon($get('start_date'));
                                        }
                                    })
                                    ->afterStateUpdated(function (Closure $set, callable $get, $state) {
                                        $start = Carbon::parse($get('start_date'));
                                        $end = Carbon::parse($state);
                                        $diff = $start->diffInDays($end);
                                        if ($get('salary_type') == 'DAILY') {
                                            $set('total_days', $diff);
                                        }
                                    }),
                            ])
                            ->disabled(function (callable $get) {
                                if ($get('salary_type') == 'DAILY') {
                                    return false;
                                }
                                return true;
                            }),
                        Forms\Components\TextInput::make('total_days')
                            ->label(function (callable $get) {
                                if ($get('salary_type') == 'DAILY') {
                                    return 'Total in Days';
                                }
                                return 'Total in Month';
                            })
                            ->required()
                            ->minValue(0)
                            ->reactive()
                            ->disabled(function (callable $get) {
                                if ($get('salary_type') == 'DAILY') {
                                    return false;
                                }
                                return true;
                            })
                            ->required(function (callable $get) {
                                if ($get('unit_price') > 0) {
                                    return true;
                                }
                                return false;
                            }),
                        Forms\Components\TextInput::make('unit_price')
                            ->numeric()
                            ->disabled()
                            ->mask(
                                fn (Mask $mask) => $mask
                                    ->numeric()
                                    ->decimalPlaces(2)
                                    ->decimalSeparator(',')
                                    ->thousandsSeparator(',')
                            ),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('total_days_overtime')
                                    ->reactive()
                                    ->minValue(0)
                                    ->numeric()
                                    ->required(function (callable $get) {
                                        if ($get('overtime') > 0) {
                                            return true;
                                        }
                                        return false;
                                    }),
                                Forms\Components\TextInput::make('overtime')
                                    ->numeric()
                                    ->disabled()
                                    ->mask(
                                        fn (Mask $mask) => $mask
                                            ->numeric()
                                            ->decimalPlaces(2)
                                            ->decimalSeparator(',')
                                            ->thousandsSeparator(',')
                                    ),
                            ])
                            ->hidden(function (callable $get) {
                                if ($get('overtime') > 0) {
                                    return false;
                                }
                                return true;
                            }),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('total_days_support')
                                    ->reactive()
                                    ->minValue(0)
                                    ->numeric()
                                    ->required(function (callable $get) {
                                        if ($get('support_price') > 0) {
                                            return true;
                                        }
                                        return false;
                                    }),
                                Forms\Components\TextInput::make('support_price')
                                    ->numeric()
                                    ->disabled()
                                    ->mask(
                                        fn (Mask $mask) => $mask
                                            ->numeric()
                                            ->decimalPlaces(2)
                                            ->decimalSeparator(',')
                                            ->thousandsSeparator(',')
                                    ),
                            ])
                            ->hidden(function (callable $get) {
                                if ($get('support_price') > 0) {
                                    return false;
                                }
                                return true;
                            }),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('total_days_cor')
                                    ->reactive()
                                    ->minValue(0)
                                    ->numeric()
                                    ->required(function (callable $get) {
                                        if ($get('cor_price') > 0) {
                                            return true;
                                        }
                                        return false;
                                    }),
                                Forms\Components\TextInput::make('cor_price')
                                    ->numeric()
                                    ->disabled()
                                    ->mask(
                                        fn (Mask $mask) => $mask
                                            ->numeric()
                                            ->decimalPlaces(2)
                                            ->decimalSeparator(',')
                                            ->thousandsSeparator(',')
                                    ),
                            ])
                            ->hidden(function (callable $get) {
                                if ($get('cor_price') > 0) {
                                    return false;
                                }
                                return true;
                            }),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('loan_payment')
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $get, Closure $set, $state) {
                                        $outstanding = (float)$get('total_loan') - (float)$state;
                                        $set('outstanding', (string)$outstanding);
                                    })
                                    ->numeric()
                                    ->mask(
                                        fn (Mask $mask) => $mask
                                            ->numeric()
                                            ->decimalPlaces(2)
                                            ->decimalSeparator(',')
                                            ->thousandsSeparator(',')
                                    )
                                    ->required(function (callable $get) {
                                        if ($get('total_loan') > 0) {
                                            return true;
                                        }
                                        return false;
                                    }),
                                Forms\Components\TextInput::make('total_loan')
                                    ->numeric()
                                    ->disabled()
                                    ->mask(
                                        fn (Mask $mask) => $mask
                                            ->numeric()
                                            ->decimalPlaces(2)
                                            ->decimalSeparator(',')
                                            ->thousandsSeparator(',')
                                    ),
                                Forms\Components\TextInput::make('outstanding')
                                    ->dehydrated(false)
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->columnSpanFull()
                                    ->mask(
                                        fn (Mask $mask) => $mask
                                            ->numeric()
                                            ->decimalPlaces(2)
                                            ->decimalSeparator(',')
                                            ->thousandsSeparator(',')
                                    ),
                            ])
                            ->hidden(function (callable $get) {
                                if ($get('total_loan') > 0) {
                                    return false;
                                }
                                return true;
                            }),
                        Card::make([
                            Placeholder::make('total_gross_salary')
                                ->content(function (callable $get) {
                                    $data = static::getCalcPayroll($get);
                                    return 'Rp ' . number_format($data['total_gross'] ?? 0, 0, ',', '.');
                                }),
                            Placeholder::make('total_net_salary')
                                ->content(function (callable $get) {
                                    $data = static::getCalcPayroll($get);
                                    return 'Rp ' . number_format($data['total_net'] ?? 0, 0, ',', '.');
                                }),

                        ])->columns(2),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->maxLength(500),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('salary_type')
                    ->color(fn (string $state): string => $state == 'DAILY' ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('employee.employee_name'),
                Tables\Columns\TextColumn::make('employee.nik'),
                Tables\Columns\TextColumn::make('employee.department'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date(),
                Tables\Columns\TextColumn::make('total_days')->label('Total')
                    ->formatStateUsing(function (string $state, Model $record): string {
                        $type =  $record->salary_type == 'DAILY' ? 'Day(s)' : 'Month';
                        return $state . " " . $type;
                    }),
                Tables\Columns\TextColumn::make('unit_price')->money('idr', true),
                Tables\Columns\TextColumn::make('total_days_overtime'),
                Tables\Columns\TextColumn::make('overtime')->money('idr', true),
                Tables\Columns\TextColumn::make('total_days_support'),
                Tables\Columns\TextColumn::make('support_price')->money('idr', true),
                Tables\Columns\TextColumn::make('total_days_cor'),
                Tables\Columns\TextColumn::make('cor_price')->money('idr', true),
                Tables\Columns\TextColumn::make('total_loan')->money('idr', true),
                Tables\Columns\TextColumn::make('loan_payment')->money('idr', true),
                Tables\Columns\TextColumn::make('outstanding')->money('idr', true),
                Tables\Columns\TextColumn::make('total_gross_salary')->money('idr', true),
                Tables\Columns\TextColumn::make('total_net_salary')->money('idr', true),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $header = $livewire->ownerRecord;
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        return $header->is_jurnal == 0 && $isEdit;
                    })
                    ->after(function (RelationManager $livewire, Model $record) {
                        $details = EmployeePayrollDetail::where('payroll_id', $record->payroll_id);
                        $header = EmployeePayroll::find($record->payroll_id);
                        $header->payroll_total = $details->sum('total_gross_salary');
                        $header->payment_loan_total = $details->sum('loan_payment');
                        $header->save();
                        $livewire->emit('refresh');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $header = $livewire->ownerRecord;
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        return $header->is_jurnal == 0 && $isEdit;
                    })
                    ->after(function (RelationManager $livewire, Model $record) {
                        $details = EmployeePayrollDetail::where('payroll_id', $record->payroll_id);
                        $header = EmployeePayroll::find($record->payroll_id);
                        $header->payroll_total = $details->sum('total_gross_salary');
                        $header->payment_loan_total = $details->sum('loan_payment');
                        $header->save();
                        $livewire->emit('refresh');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $header = $livewire->ownerRecord;
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        return $header->is_jurnal == 0 && $isEdit;
                    })
                    ->after(function (RelationManager $livewire, Model $record) {
                        $details = EmployeePayrollDetail::where('payroll_id', $record->payroll_id);
                        $header = EmployeePayroll::find($record->payroll_id);
                        $header->payroll_total = $details->sum('total_gross_salary');
                        $header->payment_loan_total = $details->sum('loan_payment');
                        $header->save();
                        $livewire->emit('refresh');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(function (RelationManager $livewire) {
                        $header = $livewire->ownerRecord;
                        $isEdit = Str::contains($livewire->pageClass, '\Edit');
                        return $header->is_jurnal && $isEdit;
                    })
                    ->after(function (RelationManager $livewire, Model $record) {
                        $details = EmployeePayrollDetail::where('payroll_id', $record->payroll_id);
                        $header = EmployeePayroll::find($record->payroll_id);
                        $header->payroll_total = $details->sum('total_gross_salary');
                        $header->payment_loan_total = $details->sum('loan_payment');
                        $header->save();
                        $livewire->emit('refresh');
                    }),
            ]);
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 15, 20];
    }

    public static function getCalcPayroll(callable $get): array
    {
        $salary = (float)$get('total_days') * (float)$get('unit_price');
        $overtime = (float)$get('total_days_overtime') * (float)$get('overtime');
        $support = (float)$get('total_days_support') * (float)$get('support_price');
        $cor = (float)$get('total_days_cor') * (float)$get('cor_price');
        $outstanding = (float)$get('total_loan') - (float)$get('loan_payment');
        $total_gross = $salary + $overtime + $support + $cor;
        $total_net = $total_gross - (float)$get('loan_payment');
        return [
            'salary' => $salary,
            'overtime' => $overtime,
            'support' => $support,
            'cor' => $cor,
            'outstanding' => $outstanding,
            'total_gross' => $total_gross,
            'total_net' => $total_net,
        ];
    }
}
