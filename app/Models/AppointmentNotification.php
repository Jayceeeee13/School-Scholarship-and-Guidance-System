<?php
// app/Models/AppointmentNotification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentNotification extends Model
{
    protected $table = 'appointment_notifications'; // ← change this

    protected $fillable = [
        'user_id',
        'counseling_appointment_id',
        'referral_invitation_id',
        'type',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(CounselingAppointments::class, 'counseling_appointment_id');
    }

    public function referralInvitation(): BelongsTo
    {
        return $this->belongsTo(ReferralInvitation::class, 'referral_invitation_id');
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Create a referral invitation notification for the student linked to the referral.
     */
    public static function notifyReferral(ReferralInvitation $invitation, string $type, string $message): void
    {
        $referral = $invitation->referral;

        $user = User::whereHas('student', function ($q) use ($referral) {
            $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$referral->name}%"]);
        })->first();

        if (!$user) return;

        static::create([
            'user_id'                   => $user->id,
            'referral_invitation_id'    => $invitation->id,
            'counseling_appointment_id' => null,
            'type'                      => $type,
            'message'                   => $message,
        ]);
    }

    /**
     * Create a referral status notification (approved/rejected) for the student
     * linked to the referral. Mirrors notifyReferral() but for the referral
     * record itself — there's no invitation involved here.
     */
    public static function notifyReferralStatus(Referrals $referral, string $type, string $message): void
    {
        $user = User::whereHas('student', function ($q) use ($referral) {
            $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$referral->name}%"]);
        })->first();

        if (!$user) return;

        static::create([
            'user_id'                   => $user->id,
            'referral_invitation_id'    => null,
            'counseling_appointment_id' => null,
            'type'                      => $type,
            'message'                   => $message,
        ]);
    }
}