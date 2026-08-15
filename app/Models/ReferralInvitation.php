<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralInvitation extends Model
{
    protected $fillable = [
        'referral_id',
        'session_date',
        'time_slot_id',
        'purpose',
        'personnel_id',
        'status',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referrals::class, 'referral_id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(CounselingTimeSlot::class, 'time_slot_id');
    }

    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnels::class, 'personnel_id');
    }
}