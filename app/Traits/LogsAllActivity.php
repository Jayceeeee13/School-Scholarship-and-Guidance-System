<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsAllActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logExcept(['archived_at'])
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