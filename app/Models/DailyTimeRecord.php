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
    ];

    protected $casts = [
        'date'        => 'date',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
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

    /**
     * "MONTH:" on the paper form — derived from `date`, no separate column needed.
     */
    public function getMonthLabelAttribute(): ?string
    {
        return $this->date?->format('F Y');
    }

    /**
     * Computes total hours worked from the AM/PM in-out pairs.
     * Any pair missing either its "in" or "out" time is simply skipped
     * (not counted), rather than throwing — so a half-filled entry still
     * gives a partial total instead of failing.
     *
     * FIX: previously called diffInMinutes() with the "out" time as the
     * base and "in" time as the argument (Carbon::parse($amOut)->diffInMinutes($amIn)).
     * Carbon's $a->diffInMinutes($b) returns a SIGNED value equal to
     * ($b - $a), so that produced (amIn - amOut), i.e. a negative number,
     * instead of the intended (amOut - amIn). Swapped the call order so
     * "in" is the base and "out" is the argument, and added absolute: true
     * as a safety net so the total can never go negative even if a time
     * pair is entered out of order.
     */
    public static function calculateTotalHours(
        ?string $amIn,
        ?string $amOut,
        ?string $pmIn,
        ?string $pmOut
    ): float {
        $hours = 0.0;

        if ($amIn && $amOut) {
            $hours += Carbon::parse($amIn)->diffInMinutes(Carbon::parse($amOut), absolute: true) / 60;
        }

        if ($pmIn && $pmOut) {
            $hours += Carbon::parse($pmIn)->diffInMinutes(Carbon::parse($pmOut), absolute: true) / 60;
        }

        return round($hours, 2);
    }
}