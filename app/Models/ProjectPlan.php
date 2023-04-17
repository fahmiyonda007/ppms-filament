<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPlan extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'code',
        'name',
        'description',
        'start_project',
        'est_end_project',
        'end_project',
        'progress',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'start_project',
        'est_end_project',
        'end_project',
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

    public function projectPlanDetails(): HasMany
    {
        return $this->hasMany(ProjectPlanDetail::class, 'project_plan_id', 'id');
    }

    public function projectCost(): HasMany
    {
        return $this->hasMany(ProjectCost::class, 'project_plan_id', 'id');
    }

}
