<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcement';
    public $timestamps = true;

    protected $fillable = [
        'announcement',
        'is_publish',
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
