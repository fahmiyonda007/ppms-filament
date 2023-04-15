<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoaSecond extends Model
{
    use HasFactory;

    protected $table = 'coa_level_seconds';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'name',
        'level_first_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [];

    protected $hidden = [
        'level_first_id',
        'created_at',
        'updated_at',
    ];

    public function first(): BelongsTo
    {
        return $this->belongsTo(CoaFirst::class, 'level_first_id');
    }

    public function thirds(): BelongsToMany
    {
        return $this->belongsToMany(CoaThird::class, 'level_second_id', 'id');
    }

}
