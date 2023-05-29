<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorLiability extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'transaction_code',
        'transaction_date',
        'scope_of_work',
        'description',
        'est_price',
        'deal_price',
        'start_date',
        'est_end_date',
        'end_date',
        'project_status',
        'outstanding',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'transaction_date',
        'start_date',
        'est_end_date',
        'end_date',
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

    public function vendorLiabilityPayments(): HasMany
    {
        return $this->hasMany(VendorLiabilityPayment::class, 'vendor_liabilities_id', 'id');
    }
    public function projectPlan(): BelongsTo
    {
        return $this->belongsTo(ProjectPlan::class, 'project_plan_id');
    }
}
