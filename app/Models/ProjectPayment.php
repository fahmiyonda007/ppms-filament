<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPayment extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'project_plan_id',
        'transaction_code',
        'project_plan_detail_id',
        'booking_by',
        'booking_date',
        'payment_type',
        'kpr_type',
        'sales_id',
        'description',
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
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    public function projectPaymentDetails(): HasMany
    {
        return $this->hasMany(ProjectPaymentDetail::class, 'project_payment_id', 'id');
    }

    public function projectPlan(): BelongsTo
    {
        return $this->belongsTo(ProjectPlan::class, 'project_plan_id');
    }

    public function projectPlanDetail(): BelongsTo
    {
        return $this->belongsTo(ProjectPlanDetail::class, 'project_plan_detail_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'booking_by');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'sales_id');
    }
}
