<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'bank_name',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
