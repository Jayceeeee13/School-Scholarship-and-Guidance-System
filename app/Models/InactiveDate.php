<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InactiveDate extends Model
{
    protected $fillable = ['date', 'title', 'reason'];

    protected $casts = [
        'date' => 'date',
    ];

    public static function isInactive(string $date): bool
    {
        return self::whereDate('date', $date)->exists();
    }

    public static function getInactiveDates(): array
    {
        return self::pluck('date')->map(fn ($d) => $d->format('Y-m-d'))->unique()->values()->toArray();
    }

    public static function getEventsForDate(string $date)
    {
        return self::whereDate('date', $date)->orderBy('id')->get();
    }
}