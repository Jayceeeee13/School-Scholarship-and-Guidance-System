<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Endorsement extends Model
{
    protected $table = 'endorsements';

    protected $fillable = [
        'counseling_appointment_id',
        'referral_id',
        'date',
        'to_where',
        'from_where',
        'issue',
        'personnel_id',
        'received_by',
        'receive_date',
    ];

    protected $casts = [
        'date'         => 'date',
        'receive_date' => 'date',
    ];

    // If you add a counseling_appointment_id FK later:
    public function counselingAppointment(): BelongsTo
    {
        return $this->belongsTo(CounselingAppointments::class, 'counseling_appointment_id');
    }

    // If you add a referral_id FK later:
    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referrals::class, 'referral_id');
    }

    public function personnel()
    {
        return $this->belongsTo(Personnels::class);
    }
}