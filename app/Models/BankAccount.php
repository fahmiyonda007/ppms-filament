<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BankAccount extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'account_number',
        'account_name',
        'bank_name',
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

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'vendor_bankaccount', 'vendor_id', 'bankaccount_id');
    }

    public function getFullAccountAttribute()
    {
        return $this->account_name . ' - ' . $this->account_number;
    }
}
