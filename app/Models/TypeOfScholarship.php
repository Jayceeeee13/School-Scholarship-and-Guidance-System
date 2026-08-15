<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeOfScholarship extends Model
{
    protected $fillable = [
        'name',
        'slots',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scope to get only active records
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }
}
