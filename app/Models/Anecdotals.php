<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anecdotals extends Model
{
    protected $table = 'anecdotals';

    protected $with = ['logform.appointment', 'personnel'];

    protected $fillable = [
        'counseling_logforms_id',
        'area_concern',
        'concern',
        'intervention',
        'personnel_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function logform(): BelongsTo
    {
        return $this->belongsTo(CounselingLogforms::class, 'counseling_logforms_id');
    }

    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnels::class);
    }
}