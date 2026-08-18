<?php

namespace App\Traits;

trait LogsCustomActivity
{
    /**
     * Log a custom, non-CRUD activity event (approvals, rescheduling,
     * archiving, restoring, etc.) against a given model instance.
     *
     * Skipped entirely if the acting user is a student — students should
     * never appear in the activity log.
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
        $user = auth()->user();

        if ($user && strtolower($user->role?->name ?? '') === 'student') {
            return;
        }

        activity($logName)
            ->causedBy($user)
            ->performedOn($record)
            ->event($event)
            ->withProperties($properties)
            ->log($description);
    }
}