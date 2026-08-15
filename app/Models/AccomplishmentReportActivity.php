<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccomplishmentReportActivity extends Model
{
    protected $fillable = [
        'accomplishment_report_id',
        'seq',
        'activity_date',
        'venue',
        'activity',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(AccomplishmentReport::class, 'accomplishment_report_id');
    }
}