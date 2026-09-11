<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CounselingLogforms extends Model
{
    protected $table = 'counseling_logforms';

    protected $with = [
        'appointment',
        'walkInStudent',
    ];

    protected $fillable = [
        'counseling_appointments_id',
        'referral_id',
        'type',
        'walkin_student_id',
        'support_needed_id',
        'concern',
        'remarks',
        'follow_up_required',
        'archived_at',
    ];

    protected $casts = [
        'follow_up_required' => 'boolean',
        'archived_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function (CounselingLogforms $logform) {

            if (
                $logform->type !== 'walk_in' &&
                $logform->counseling_appointments_id
            ) {
                $appointment = $logform->appointment;

                if ($appointment && $appointment->status !== 'completed') {
                    $appointment->update([
                        'status' => 'completed',
                    ]);
                }
            }

            if (
                $logform->referral_id &&
                $logform->type !== 'walk_in'
            ) {
                $referral = $logform->referral;

                if ($referral && $referral->status === 'approved') {
                    $referral->update([
                        'status' => 'completed',
                    ]);
                }
            }
        });
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(
            CounselingAppointments::class,
            'counseling_appointments_id'
        );
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(
            Referrals::class,
            'referral_id'
        );
    }

    public function walkInStudent(): BelongsTo
    {
        return $this->belongsTo(
            Students::class,
            'walkin_student_id'
        );
    }

    public function supportNeeded(): BelongsTo
    {
        return $this->belongsTo(
            SupportNeeded::class,
            'support_needed_id'
        );
    }

    public function anecdotals(): HasMany
    {
        return $this->hasMany(
            Anecdotals::class,
            'counseling_logforms_id'
        );
    }

    public function followUpAppointment(): HasOne
    {
        return $this->hasOne(
            CounselingAppointments::class,
            'source_logform_id'
        );
    }

    public function isWalkIn(): bool
    {
        return $this->type === 'walk_in';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->isWalkIn()) {
            return $this->walkInStudent
                ? trim(
                    "{$this->walkInStudent->first_name} " .
                    "{$this->walkInStudent->middle_name} " .
                    "{$this->walkInStudent->last_name}"
                )
                : '—';
        }

        if ($this->referral) {
            return $this->referral->name;
        }

        return $this->appointment
            ? trim(
                "{$this->appointment->first_name} " .
                "{$this->appointment->middle_name} " .
                "{$this->appointment->last_name}"
            )
            : '—';
    }

    public function getDisplayCourseAttribute(): string
    {
        if ($this->isWalkIn()) {
            if (! $this->walkInStudent) {
                return '—';
            }

            $program = $this->walkInStudent->program?->name ?? '';
            $year = $this->walkInStudent->year_level ?? '';

            return trim("{$program} {$year}") ?: '—';
        }

        if ($this->referral) {
            return $this->referral->course_and_year ?? '—';
        }

        return $this->appointment?->course_and_year ?? '—';
    }

    public function getDisplayContactAttribute(): string
    {
        if ($this->isWalkIn()) {
            return $this->walkInStudent?->contact_no ?? '—';
        }

        if ($this->referral) {
            return '—';
        }

        return $this->appointment?->contact_no ?? '—';
    }

    public function getDisplayAddressAttribute(): string
    {
        if ($this->isWalkIn()) {
            return $this->walkInStudent?->address ?? '—';
        }

        if ($this->referral) {
            return '—';
        }

        return $this->appointment?->present_address ?? '—';
    }
}