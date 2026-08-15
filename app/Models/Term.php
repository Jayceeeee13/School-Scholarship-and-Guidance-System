<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $fillable = [
        'school_year',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scope to get only active term
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Display label e.g. "2024-2025 - 1st Semester"
    public function getLabelAttribute(): string
    {
        return "{$this->school_year} - {$this->semester}";
    }

    public function students()
    {
        return $this->hasMany(Students::class);
    }
}