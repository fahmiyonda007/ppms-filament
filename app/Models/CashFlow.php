<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashFlow extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'transaction_code',
        'transaction_date',
        'project_plan_id',
        'coa_id',
        'cash_flow_type',
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

    protected $casts = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    public function cashFlowDetails(): HasMany
    {
        return $this->hasMany(\App\Models\CashFlowDetail::class, 'cash_flow_id', 'id');
    }

    public function projectPlan(): BelongsTo
    {
        return $this->belongsTo(ProjectPlan::class, 'project_plan_id');
    }

    public function coaThird(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id');
    }
}
