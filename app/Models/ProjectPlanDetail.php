<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'kpr_type',
        'tax_rate',
        'tax',
        'notary_fee',
        'commission_rate',
        'commission',
        'other_commission',
        'added_bonus',
        'net_price',
        'no_shm',
        'no_imb',
        'land_width',
        'building_width',
        'sales_id',
        'coa_id_source',
        'is_jurnal',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'booking_date',
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

    public function projectPlan(): BelongsTo
    {
        return $this->belongsTo(ProjectPlan::class, 'project_plan_id');
    }

    public function projectPlanDetailPayments(): HasMany
    {
        return $this->hasMany(ProjectPlanDetailPayment::class, 'plan_detail_id', 'id');
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
