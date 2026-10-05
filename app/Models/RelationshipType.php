<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RelationshipType extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referrals::class);
    }

    public function isOther(): bool
    {
        return strtolower(trim($this->name)) === 'other';
    }
}