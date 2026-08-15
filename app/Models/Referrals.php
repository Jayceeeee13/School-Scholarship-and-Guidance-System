<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referrals extends Model
{
    protected $table = 'referrals';

    protected $fillable = [
        'date',
        'name',
        'course_and_year',
        'age',
        'case_presented',
        'referred_by',
        'status',
        'archived_at',
    ];

    protected $casts = [
        'date'        => 'date',
        'age'         => 'integer',
        'archived_at' => 'datetime',
    ];

    // Relationships
    public function logforms(): HasMany
    {
        return $this->hasMany(CounselingLogforms::class, 'referral_id');
    }

    public function endorsement()
    {
        return $this->hasOne(\App\Models\Endorsement::class, 'referral_id');
    }

    public function invitation()
    {
        return $this->hasOne(\App\Models\ReferralInvitation::class, 'referral_id');
    }
}