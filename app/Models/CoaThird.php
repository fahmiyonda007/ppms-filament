<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoaThird extends Model
{
    use HasFactory;

    protected $table = 'coa_level_thirds';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'name',
        'balance',
        'level_first_id',
        'level_second_id',
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
        'level_first_id',
        'level_second_id',
        'created_by',
        'updated_by',
    ];

    public function first(): BelongsTo
    {
        return $this->belongsTo(CoaFirst::class, 'level_first_id');
    }

    public function second(): BelongsTo
    {
        return $this->belongsTo(CoaSecond::class, 'level_second_id');
    }

    public function getFullNameAttribute()
    {
        return $this->code . ' - ' . $this->name;
    }
}
