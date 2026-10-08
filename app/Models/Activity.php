<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'description',
        'images',
        'activity_date',
    ];

    protected $casts = [
        'images'        => 'array',
        'activity_date' => 'date',
    ];

    /** Public URLs of all photos, in order (first one is the cover). */
    protected function photoUrls(): Attribute
    {
        return Attribute::get(
            fn () => collect($this->images ?? [])
                ->map(fn ($path) => asset('storage/' . $path))
                ->values()
                ->all()
        );
    }

    protected static function booted(): void
    {
        // Delete the photo files when an activity is deleted
        static::deleting(function (Activity $activity) {
            foreach ($activity->images ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }
        });
    }
}