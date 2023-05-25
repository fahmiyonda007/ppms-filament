<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayrollDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'payroll_id',
        'employee_id',
        'employee_nik',
        'start_date',
        'end_date',
        'salary_type',
        'total_days',
        'unit_price',
        'coa_id_loan',
        'total_days_overtime',
        'overtime',
        'total_days_support',
        'support_price',
        'total_days_cor',
        'cor_price',
        'total_loan',
        'loan_payment',
        'outstanding',
        'total_gross_salary',
        'total_net_salary',
        'description',
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    protected $casts = [];

    protected $hidden = [
    ];

    public function employeePayroll(): BelongsTo
    {
        return $this->belongsTo(EmployeePayroll::class, 'payroll_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function coaThirdLoan(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id_loan');
    }
}
