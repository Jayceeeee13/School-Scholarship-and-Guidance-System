<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Requirement extends Model
{
    protected $fillable = [
        'name',
        'type_of_application_id',
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

    public function typeOfApplication()
    {
        return $this->belongsTo(TypeOfApplication::class);
    }

    public function applicants(): BelongsToMany
{
    return $this->belongsToMany(Applicant::class, 'applicant_requirement')
        ->withPivot('is_submitted', 'file_path', 'notes')
        ->withTimestamps();
}
}
