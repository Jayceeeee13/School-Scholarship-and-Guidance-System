<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Personnels extends Model
{
    use LogsAllActivity;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'age',
        'birthdate',
        'contact_no',
        'address',
        'email',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────

    public function anecdotals()
    {
        return $this->hasMany(Anecdotals::class);
    }

    public function endorsements()
    {
        return $this->hasMany(Endorsement::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'personnel_id');
    }

    // ── Archive scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}