<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccomplishmentReport extends Model
{
    protected $fillable = [
        'scholar_id',
        'scholar_type',
        'term_id',
        'status',
        'remarks',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function scholar(): MorphTo
    {
        return $this->morphTo();
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(AccomplishmentReportActivity::class)->orderBy('seq');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}