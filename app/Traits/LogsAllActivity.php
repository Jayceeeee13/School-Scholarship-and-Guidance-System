<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsAllActivity
{
    use LogsActivity;

    /**
     * Skip automatic CRUD logging whenever the acting user is a student.
     * Toggles Spatie's per-model logging flag right before the model event
     * fires, then re-enables it immediately after so it never leaks into
     * other requests/models.
     */
    public static function bootLogsAllActivity(): void
    {
        static::saving(function () {
            if (self::isStudentActor()) {
                static::disableLogging();
            }
        });

        static::saved(function () {
            static::enableLogging();
        });

        static::deleting(function () {
            if (self::isStudentActor()) {
                static::disableLogging();
            }
        });

        static::deleted(function () {
            static::enableLogging();
        });
    }

    protected static function isStudentActor(): bool
    {
        $user = auth()->user();

        return $user && strtolower($user->role?->name ?? '') === 'student';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logExcept(['archived_at']) // archiving/restoring is logged manually with its own event name
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getActivityLogName())
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => class_basename($this) . ' created',
                'updated' => class_basename($this) . ' updated',
                'deleted' => class_basename($this) . ' deleted',
                default   => class_basename($this) . " {$eventName}",
            });
    }

    protected function getActivityLogName(): string
    {
        return Str::snake(class_basename($this));
    }
}