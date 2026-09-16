<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyTimeRecord extends Model
{
    protected $table = 'daily_time_records';

    protected $fillable = [
        'scholar_id',
        'office_assigned',
        'date',
        'am_in',
        'am_in_location',
        'am_out',
        'am_out_location',
        'pm_in',
        'pm_in_location',
        'pm_out',
        'pm_out_location',
        'total_hours',
        'status',
        'remarks',
        'approved_by_id',
        'approved_at',
        'received_by_id',
        'received_at',
        'archived_at',
    ];

    protected $casts = [
        'date'        => 'date',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function scholar(): BelongsTo
    {
        return $this->belongsTo(Scholars::class, 'scholar_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    public function getMonthLabelAttribute(): ?string
    {
        return $this->date?->format('F Y');
    }

    public static function calculateTotalHours(
        ?string $amIn,
        ?string $amOut,
        ?string $pmIn,
        ?string $pmOut
    ): float {
        $hours = 0.0;

        if ($amIn && $amOut) {
            $hours += Carbon::parse($amOut)->diffInMinutes(Carbon::parse($amIn)) / 60;
        }

        if ($pmIn && $pmOut) {
            $hours += Carbon::parse($pmOut)->diffInMinutes(Carbon::parse($pmIn)) / 60;
        }

        return round($hours, 2);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }
}