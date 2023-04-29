<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralJournal extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'project_plan_id',
        'jurnal_id',
        'reference_code',
        'description',
        'transaction_date',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'transaction_date',
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

    public function generalJournalDetails(): HasMany
    {
        return $this->hasMany(GeneralJournalDetail::class, 'jurnal_id', 'id');
    }
}
