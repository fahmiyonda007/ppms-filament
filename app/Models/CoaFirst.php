<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoaFirst extends Model
{
    use HasFactory;

    protected $table = 'coa_level_firsts';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'name',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function seconds(): HasMany
    {
        return $this->hasMany(CoaSecond::class, 'level_first_id', 'id');
    }

    public function thirds(): HasMany
    {
        return $this->hasMany(CoaThird::class, 'level_first_id', 'id');
    }
}
