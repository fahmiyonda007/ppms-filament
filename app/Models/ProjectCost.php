<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectCost extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'project_plan_id',
        'transaction_code',
        'description',
        'order_date',
        'payment_date',
        'payment_status',
        'vendor_id',
        'coa_id_source1',
        'coa_id_source2',
        'coa_id_source3',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'order_date',
        'payment_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [];

    protected $hidden = [
        'project_plan_id',
        'vendor_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    public function projectCostDetails(): HasMany
    {
        return $this->hasMany(ProjectCostDetail::class, 'project_cost_id', 'id');
    }

    public function projectPlan(): BelongsTo
    {
        return $this->belongsTo(ProjectPlan::class, 'project_plan_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
