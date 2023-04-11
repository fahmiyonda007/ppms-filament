<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'bankaccount_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'bankaccount_id' => 'integer',
    ];

    protected $hidden = [
        'bankaccount_id',
        'created_at',
        'updated_at',
    ];

}
