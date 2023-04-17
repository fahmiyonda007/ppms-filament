<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPlanDetail extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'project_plan_id',
        'unit_kavling',
        'unit_price',
        'description',
        'booking_by',
        'booking_date',
        'deal_price',
        'down_payment',
        'payment_type',
        'tax',
        'notary_fee',
        'commission',
        'other_commission',
        'net_price',
        'sales_id',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'booking_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [];

    protected $hidden = [
        'project_plan_id',
        'sales_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    public function projectPlan(): BelongsTo
    {
        return $this->belongsTo(ProjectPlan::class, 'project_plan_id');
    }

}