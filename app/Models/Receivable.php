<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receivable extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'transaction_date',
        'employee_id',
        'total_loan',
        'payment_amount',
        'outstanding',
        'is_jurnal',
        'coa_id_source',
        'coa_id_destination',
        'description',
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function coaThirdSource(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id_source');
    }

    public function coaThirdDestination(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id_destination');
    }
}
