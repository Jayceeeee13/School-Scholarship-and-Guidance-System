<?php
// app/Models/CounselingAppointments.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselingAppointments extends Model
{
    protected $table = 'counseling_appointments';

    protected $fillable = [
        'parent_appointment_id',  // ← links a follow-up back to its original
        'student_id',            // FK → students.id
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
        'approved_at'      => 'datetime',
        'archived_at'      => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function logforms(): HasMany
    {
        return $this->hasMany(CounselingLogforms::class);
    }

    public function endorsement(): HasOne
    {
        return $this->hasOne(Endorsement::class, 'counseling_appointment_id');
    }

    public function modeOfCounseling(): BelongsTo
    {
        return $this->belongsTo(ModeOfCounseling::class);
    }

    public function supportNeeded(): BelongsTo
    {
        return $this->belongsTo(SupportNeeded::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(CounselingTimeSlot::class);
    }

    public function portalNotifications(): HasMany
    {
        return $this->hasMany(AppointmentNotification::class, 'counseling_appointment_id');
    }

    // ── Follow-up Relationships ─────────────────────────────────────────────────

    public function parentAppointment(): BelongsTo
    {
        return $this->belongsTo(CounselingAppointments::class, 'parent_appointment_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(CounselingAppointments::class, 'parent_appointment_id');
    }

    public function isFollowUp(): bool
    {
        return ! is_null($this->parent_appointment_id);
    }

    // ── Archive scopes ───────────────────────────────────────────────────────

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

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Whether this appointment can still be cancelled right now.
     *
     * - Pending appointments can always be cancelled.
     * - Approved appointments can only be cancelled within 5 hours of
     *   approval. If approved_at wasn't set (older records before this
     *   feature existed), we allow cancellation as a safe fallback.
     */
    public function canBeCancelled(): bool
    {
        if ($this->status === 'pending') {
            return true;
        }

        if ($this->status === 'approved') {
            if (! $this->approved_at) {
                return true;
            }

            return now()->lt($this->approved_at->copy()->addHours(5));
        }

        return false;
    }

    /**
     * Resolve User via:
     * counseling_appointments.student_id → students.id → students.user_id → users.id
     */
    public function resolveUser(): ?User
    {
        $this->load('student.user');
        return $this->student?->user;
    }

    /**
     * Push approval/rejection notification to the student portal.
     */
    public function notifyStudent(string $type): void
    {
        $user = $this->resolveUser();

        if (!$user) return;  // No linked portal account — skip silently

        $date = $this->counseling_date->format('F d, Y');

        $messages = [
            'approved'    => "✅ Your counseling appointment on {$date} has been approved.",
            'rejected'    => "❌ Your counseling appointment on {$date} has been rejected.",
            'rescheduled' => "🔄 Your counseling appointment has been rescheduled to {$date}.",
        ];

        AppointmentNotification::create([
            'user_id'                   => $user->id,
            'counseling_appointment_id' => $this->id,
            'type'                      => $type,
            'message'                   => $messages[$type] ?? 'Your appointment status has been updated.',
        ]);
    }

    /**
     * Push a notification into the Filament admin/guidance database
     * notification bell, for admin-facing appointment lifecycle events.
     *
     * IMPORTANT: this uses Filament\Notifications\Notification::make()->
     * sendToDatabase($admin) rather than hand-building the `data` array,
     * because Filament's bell component expects a very specific payload
     * shape (title, body, icon, color, duration, view, viewData, format,
     * actions, iconColor). A hand-built array missing any of those keys
     * (as this method previously did) can break rendering for the WHOLE
     * notification list in the bell, not just the malformed entry —
     * sendToDatabase() guarantees the correct shape every time.
     */
    public function notifyAdmin(string $type): void
{
    \App\Notifications\AppointmentStatusNotification::send($this, $type);
}
}