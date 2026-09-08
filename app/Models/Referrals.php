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

    public function logforms(): HasMany
    {
        return $this->hasMany(
            CounselingLogforms::class,
            'referral_id'
        );
    }

    public function endorsement()
    {
        return $this->hasOne(
            \App\Models\Endorsement::class,
            'referral_id'
        );
    }

    public function invitation()
    {
        return $this->hasOne(
            \App\Models\ReferralInvitation::class,
            'referral_id'
        );
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, ['pending', 'rejected'], true);
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, ['pending', 'approved'], true);
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'approved';
    }

    public function markAsApproved(): bool
    {
        if (! $this->canBeApproved()) {
            return false;
        }

        return $this->update([
            'status' => 'approved',
        ]);
    }

    public function markAsRejected(): bool
    {
        if (! $this->canBeRejected()) {
            return false;
        }

        return $this->update([
            'status' => 'rejected',
        ]);
    }

    public function markAsCompleted(): bool
    {
        if (! $this->canBeCompleted()) {
            return false;
        }

        return $this->update([
            'status' => 'completed',
        ]);
    }
}