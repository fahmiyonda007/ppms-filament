<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Vendor extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'bankaccount_id',
        'created_by',
        'updated_by',
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
        'created_by',
        'updated_by',
    ];

    public function bankaccounts(): BelongsToMany
    {
        return $this->belongsToMany(BankAccount::class, 'vendor_bankaccount', 'bankaccount_id', 'vendor_id');
    }
}
