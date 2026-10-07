<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeOfScholarship extends Model
{
    protected $fillable = [
        'name',
        'slots',
        'is_active',
        'uses_dtr',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'uses_dtr'  => 'boolean',
    ];

    // Scope to get only active records
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope to get types that use DTR
    public function scopeUsesDtr($query)
    {
        return $query->where('uses_dtr', true);
    }

    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }

    /**
     * Names of every scholarship type that uses DTR.
     */
    public static function dtrNames(): array
    {
        return static::usesDtr()->pluck('name')->all();
    }

    /**
     * Case/whitespace-insensitive check for whether a scholar's
     * type_of_scholarship string is one of the DTR types.
     */
    public static function nameUsesDtr(?string $name): bool
    {
        $needle = strtolower(trim($name ?? ''));

        if ($needle === '') {
            return false;
        }

        return in_array(
            $needle,
            array_map(fn ($n) => strtolower(trim($n)), static::dtrNames()),
            true
        );
    }
}