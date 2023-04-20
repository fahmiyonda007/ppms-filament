<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCostDetail extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'project_cost_id',
        'coa_id',
        'uom',
        'qty',
        'unit_price',
        'amount',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
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

    public function projectCost(): BelongsTo
    {
        return $this->belongsTo(ProjectCost::class, 'project_cost_id');
    }

    public function coaThird(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id');
    }
}
