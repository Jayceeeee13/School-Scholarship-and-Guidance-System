<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselingAppointments extends Model
{
    protected $table = 'counseling_appointments';

    protected $fillable = [
        'parent_appointment_id',
        'source_logform_id',
        'referral_id',
        'student_id',
        'last_name',
        'first_name',
        'middle_name',
        'course_and_year',
        'contact_no',
        'present_address',
        'counseling_date',
        'time_slot_id',
        'mode_of_counseling_id',
        'support_needed_id',
        'concern',
        'status',
        'approved_at',
        'archived_at',
    ];

    protected $casts = [
        'counseling_date' => 'date',
        'approved_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            Students::class,
            'student_id'
        );
    }

    public function logforms(): HasMany
    {
        return $this->hasMany(
            CounselingLogforms::class,
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

    public function endorsement(): HasOne
    {
        return $this->hasOne(
            Endorsement::class,
            'counseling_appointment_id'
        );
    }

    public function modeOfCounseling(): BelongsTo
    {
        return $this->belongsTo(
            ModeOfCounseling::class
        );
    }

    public function supportNeeded(): BelongsTo
    {
        return $this->belongsTo(
            SupportNeeded::class
        );
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(
            CounselingTimeSlot::class
        );
    }

    public function portalNotifications(): HasMany
    {
        return $this->hasMany(
            AppointmentNotification::class,
            'counseling_appointment_id'
        );
    }

    public function parentAppointment(): BelongsTo
    {
        return $this->belongsTo(
            CounselingAppointments::class,
            'parent_appointment_id'
        );
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(
            CounselingAppointments::class,
            'parent_appointment_id'
        );
    }

    public function sourceLogform(): BelongsTo
    {
        return $this->belongsTo(
            CounselingLogforms::class,
            'source_logform_id'
        );
    }

    public function isFollowUp(): bool
    {
        return ! is_null($this->parent_appointment_id);
    }

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

    public function getFullNameAttribute(): string
    {
        return trim(
            "{$this->first_name} {$this->middle_name} {$this->last_name}"
        );
    }

    public function canBeCancelled(): bool
    {
        if ($this->status === 'pending') {
            return true;
        }

        if ($this->status === 'approved') {
            if (! $this->approved_at) {
                return true;
            }

            return now()->lt(
                $this->approved_at->copy()->addHours(3)
            );
        }

        return false;
    }

    public function resolveUser(): ?User
    {
        $this->load('student.user');

        return $this->student?->user;
    }

    public function notifyStudent(string $type): void
    {
        $user = $this->resolveUser();

        if (! $user) {
            return;
        }

        $date = $this->counseling_date->format('F d, Y');

        $messages = [
            'approved' =>
                "✅ Your counseling appointment on {$date} has been approved.",

            'rejected' =>
                "❌ Your counseling appointment on {$date} has been rejected.",

            'rescheduled' =>
                "🔄 Your counseling appointment has been rescheduled to {$date}.",

            'follow_up_scheduled' =>
                "🔄 A follow-up counseling appointment has been scheduled for {$date}.",
        ];

        AppointmentNotification::create([
            'user_id' => $user->id,
            'counseling_appointment_id' => $this->id,
            'type' => $type,
            'message' =>
                $messages[$type]
                ?? 'Your appointment status has been updated.',
        ]);
    }

    public function notifyAdmin(string $type): void
    {
        \App\Notifications\AppointmentStatusNotification::send(
            $this,
            $type
        );
    }
}