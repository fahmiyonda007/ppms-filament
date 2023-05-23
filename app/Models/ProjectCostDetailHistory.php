<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCostDetailHistory extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'vendor_id',
        'coa_id',
        'uom',
        'unit_price',
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
}
