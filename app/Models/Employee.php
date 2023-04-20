<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'nik',
        'ktp',
        'employee_name',
        'phone',
        'address',
        'email',
        'department',
        'join_date',
        'salary_day',
        'overtime',
        'total_loan',
        'bank_account_id',
        'is_resign',
        'resign_date',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'join_date',
        'resign_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
