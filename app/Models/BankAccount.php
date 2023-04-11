<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'account_number',
        'account_name',
        'bank_id',
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

    public function banks(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
