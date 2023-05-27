<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepositVendor extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'transaction_code',
        'transaction_date',
        'vendor_id',
        'coa_id_source',
        'coa_id_destination',
        'amount',
        'source_start_balance',
        'source_end_balance',
        'source_end_balance',
        'destination_start_balance',
        'destination_end_balance',
        'description',
        'is_jurnal',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'transaction_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_jurnal' => 'boolean'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function coaThirdSource(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id_source');
    }

    public function coaThirdDestination(): BelongsTo
    {
        return $this->belongsTo(CoaThird::class, 'coa_id_destination');
    }
}
