<?php

namespace App\Traits;

use App\Models\AccomplishmentReport;
use App\Models\DailyTimeRecord;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasScholarProfile
{
    // ── Relationships ────────────────────────────────────────────────

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function departmentHead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'department_head_id');
    }

    public function dailyTimeRecords(): HasMany
    {
        return $this->hasMany(DailyTimeRecord::class, 'scholar_id');
    }

    /**
     * Polymorphic so accomplishment reports work whether the scholar
     * lives in `scholars` or `institutional_scholars`.
     */
    public function accomplishmentReports(): MorphMany
    {
        return $this->morphMany(AccomplishmentReport::class, 'scholar');
    }

    // ── Eligibility & resolution helpers ────────────────────────────

    public function isEligibleForAccomplishmentReports(): bool
    {
        $type = trim($this->type_of_scholarship ?? '');

        foreach (self::ACCOMPLISHMENT_ELIGIBLE_TYPES as $eligible) {
            if (strcasecmp($type, $eligible) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find the record belonging to a portal User.
     * Prefers the direct user_id link; falls back to name matching
     * (tolerant of middle names and extra whitespace) for scholars
     * not yet explicitly linked by an admin.
     */
    public static function forUser(User $user): ?self
    {
        $byLink = self::where('user_id', $user->id)
            ->where('status', '!=', 'revoked')
            ->first();

        if ($byLink) {
            return $byLink;
        }

        $normalizedUserName = self::normalizeName($user->name);

        return self::where('status', '!=', 'revoked')
            ->get()
            ->first(function ($scholar) use ($normalizedUserName) {
                $fullName = trim("{$scholar->first_name} {$scholar->middle_name} {$scholar->last_name}");
                $altName  = trim("{$scholar->first_name} {$scholar->last_name}");

                return self::normalizeName($fullName) === $normalizedUserName
                    || self::normalizeName($altName) === $normalizedUserName;
            });
    }

    protected static function normalizeName(string $name): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($name)));
    }

    // ── Computed Attributes ──────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        $name = trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
        if ($this->extension_name) {
            $name .= ' ' . $this->extension_name;
        }
        return $name;
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    public function scopeNotRevoked($query)
    {
        return $query->where('status', '!=', 'revoked');
    }

    public function scopeByBatch($query, $batchNo)
    {
        return $query->where('batch_no', $batchNo);
    }

    // ── Helper Methods ────────────────────────────────────────────────

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public static function generateStudentId(): string
    {
        $year = date('Y');
        $lastScholar = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastScholar && $lastScholar->student_id) {
            preg_match('/SCH-(\d{4})-(\d{4})/', $lastScholar->student_id, $matches);
            $nextNumber = isset($matches[2])
                ? str_pad((int) $matches[2] + 1, 4, '0', STR_PAD_LEFT)
                : '0001';
        } else {
            $nextNumber = '0001';
        }

        return "SCH-{$year}-{$nextNumber}";
    }
}