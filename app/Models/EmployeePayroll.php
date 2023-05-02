<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeePayroll extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'transaction_code',
        'transaction_date',
        'project_plan_id',
        'payroll_total',
        'payment_loan_total',
        'coa_id_source',
        'coa_id_destination',
        'coa_id_loan',
        'source_start_balance',
        'source_end_balance',
        'destination_start_balance',
        'destination_end_balance',
        'description',
        'is_jurnal',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'transaction_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_jurnal' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    public function employeePayrollDetails(): HasMany
    {
        return $this->hasMany(EmployeePayrollDetail::class, 'payroll_id', 'id');
    }

    public function projectPlan(): BelongsTo
    {
        return $this->belongsTo(ProjectPlan::class, 'project_plan_id');
    }

    public function coaThirdSource(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id_source');
    }

    public function coaThirdDestination(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id_destination');
    }

    public function coaThirdLoan(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id_loan');
    }
}
