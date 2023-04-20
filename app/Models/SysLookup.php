<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysLookup extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'group_name',
        'code',
        'name',
        'description',
    ];

    protected $dates = [];

    protected $casts = [];

    protected $hidden = [];
}
