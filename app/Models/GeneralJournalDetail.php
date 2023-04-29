<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralJournalDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'jurnal_id',
        'no_inc',
        'coa_id',
        'coa_code',
        'debet_amount',
        'credit_amount',
        'description',
    ];

    protected $dates = [];

    protected $casts = [];

    protected $hidden = [];

    public function generalJournal(): BelongsTo
    {
        return $this->belongsTo(GeneralJournal::class, 'jurnal_id');
    }
}
