<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashFlowDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'cash_flow_id',
        'coa_id',
        'amount',
        'description',
    ];

    protected $dates = [];

    protected $casts = [];

    protected $hidden = [];

    public function cashFlow(): BelongsTo
    {
        return $this->belongsTo(CashFlow::class, 'cash_flow_id');
    }

    public function coaThird(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id');
    }
}
