<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPlanDetailPayment extends Model
{
    use HasFactory;

    // public $table = 'project_plan_detail_payment';

    public $timestamps = false;

    protected $fillable = [
        'plan_detail_id',
        'transaction_date',
        'amount',
    ];

    protected $dates = [
        'transaction_date',
    ];

    protected $casts = [];

    protected $hidden = [];

    public function projectPlanDetail(): BelongsTo
    {
        return $this->belongsTo(ProjectPlanDetail::class, 'plan_detail_id');
    }

    // public function getTotalAmountAttribute()
    // {
    //     return $this->sum('amount');
    // }
}
