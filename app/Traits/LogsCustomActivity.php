<?php

namespace App\Traits;

trait LogsCustomActivity
{
    /**
     * Log a custom, non-CRUD activity event (approvals, rescheduling,
     * archiving, restoring, etc.) against a given model instance.
     *
     * Static so it can be called from both:
     *  - Resource::table() static methods (self::logCustomActivity(...))
     *  - ListRecords page instance methods ($this->logCustomActivity(...))
     */
    protected static function logCustomActivity(
        object $record,
        string $logName,
        string $event,
        string $description,
        array $properties = []
    ): void {
        activity($logName)
            ->causedBy(auth()->user())
            ->performedOn($record)
            ->event($event)
            ->withProperties($properties)
            ->log($description);
    }
}