<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorLiabilityPayment extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'vendor_liabilities_id',
        'transaction_code',
        'transaction_date',
        'inc',
        'category',
        'coa_id_source',
        'coa_id_destination',
        'amount',
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
        'is_jurnal' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    public function vendorLiability(): BelongsTo
    {
        return $this->belongsTo(VendorLiability::class, 'vendor_liabilities_id');
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
