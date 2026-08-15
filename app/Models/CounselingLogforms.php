<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CounselingLogforms extends Model
{
    protected $table = 'counseling_logforms';

    protected $with = ['appointment', 'walkInStudent'];

    protected $fillable = [
        'counseling_appointments_id',
        'referral_id',
        'type',
        'walkin_student_id',
        'support_needed_id',
        'concern',
        'remarks',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(CounselingAppointments::class, 'counseling_appointments_id');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referrals::class, 'referral_id');
    }

    public function walkInStudent(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'walkin_student_id');
    }

    public function supportNeeded(): BelongsTo
    {
        return $this->belongsTo(SupportNeeded::class, 'support_needed_id');
    }

    public function anecdotals(): HasMany
    {
        return $this->hasMany(Anecdotals::class, 'counseling_logforms_id');
    }

    public function isWalkIn(): bool
    {
        return $this->type === 'walk_in';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->isWalkIn()) {
            return $this->walkInStudent
                ? trim("{$this->walkInStudent->first_name} {$this->walkInStudent->middle_name} {$this->walkInStudent->last_name}")
                : '—';
        }

        return $this->appointment
            ? trim("{$this->appointment->first_name} {$this->appointment->middle_name} {$this->appointment->last_name}")
            : '—';
    }

    public function getDisplayCourseAttribute(): string
    {
        if ($this->isWalkIn()) {
            if (! $this->walkInStudent) return '—';
            $program = $this->walkInStudent->program?->name ?? '';
            $year    = $this->walkInStudent->year_level ?? '';
            return trim("{$program} {$year}") ?: '—';
        }

        return $this->appointment?->course_and_year ?? '—';
    }

    public function getDisplayContactAttribute(): string
    {
        if ($this->isWalkIn()) {
            return $this->walkInStudent?->contact_no ?? '—';
        }

        return $this->appointment?->contact_no ?? '—';
    }

    public function getDisplayAddressAttribute(): string
    {
        if ($this->isWalkIn()) {
            return $this->walkInStudent?->address ?? '—';
        }

        return $this->appointment?->present_address ?? '—';
    }
}