<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'started_at',
        'completed_at',
        'violations',
        'violation_count',
        'score',
        'total_points',
        'discount',
        'percentage',
        'status',
        'archived_at',
    ];

    protected $casts = [
        'violations'      => 'array',
        'violation_count' => 'integer',
        'percentage'      => 'float',
        'started_at'      => 'datetime',
        'completed_at'    => 'datetime',
        'archived_at'     => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    // ── One-attempt guard ──────────────────────────────────────────────────

    public function scopeForUserExam($query, $userId, $examId)
    {
        return $query->where('user_id', $userId)
                     ->where('exam_id', $examId)
                     ->whereIn('status', ['pending', 'completed', 'submitted']);
    }

    public static function hasAttempted(int $userId, int $examId): bool
    {
        return static::forUserExam($userId, $examId)->exists();
    }

    public static function latestAttempt(int $userId, int $examId): ?self
    {
        return static::forUserExam($userId, $examId)->latest()->first();
    }

    // ── Discount logic — SINGLE SOURCE OF TRUTH ───────────────────────────
    //
    // All discount-related display in the app reads from resolveDiscount().
    // Used by:
    //   • ExamController::submit()           — to calculate & store the value
    //   • ExamResultSlipController::print()  — to get the label for the slip
    //   • ViewApplicant infolist             — to display benefit badge
    //   • ExamAttemptResource                — admin panel badge
    //
    // Score Guidelines (as printed on the result slip FM-AAD-064):
    //   95 – 100  →  100% Tuition Fee & Misc. Discount
    //   85 –  94  →  100% Tuition Fee Discount
    //   75 –  84  →   75% Tuition Fee Discount
    //   65 –  74  →   50% Tuition Fee Discount
    //   60 –  64  →   25% Tuition Fee Discount
    //   50 –  59  →   10% Tuition Fee Discount
    //    0 –  49  →   No Discount
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Given a raw percentage score, returns the integer discount code
     * that is stored in the `discount` column.
     *
     * Discount codes:
     *   100  → "100% Tuition Fee & Misc. Discount"
     *    85  → "100% Tuition Fee Discount"
     *    75  → "75% Tuition Fee Discount"
     *    50  → "50% Tuition Fee Discount"
     *    25  → "25% Tuition Fee Discount"
     *    10  → "10% Tuition Fee Discount"
     *     0  → "No Discount"
     */
    public static function calculateDiscount(float $percentage): int
    {
        return match (true) {
            $percentage >= 95 => 100, // 100% Tuition Fee & Misc. Discount
            $percentage >= 85 => 85,  // 100% Tuition Fee Discount
            $percentage >= 75 => 75,  // 75% Tuition Fee Discount
            $percentage >= 65 => 50,  // 50% Tuition Fee Discount
            $percentage >= 60 => 25,  // 25% Tuition Fee Discount
            $percentage >= 50 => 10,  // 10% Tuition Fee Discount
            default           => 0,   // No Discount
        };
    }

    /**
     * Given the stored discount code, returns a human-readable label
     * and a Filament badge colour.
     *
     * This is the ONLY place where discount codes are mapped to labels.
     * Every part of the UI calls this method — never hard-code labels elsewhere.
     *
     * Returns: ['label' => string, 'color' => string]
     */
    public static function resolveDiscount(int $discountCode): array
    {
        return match ($discountCode) {
            100 => [
                'label' => '100% Tuition Fee & Misc. Discount',
                'color' => 'success',
            ],
            85 => [
                'label' => '100% Tuition Fee Discount',
                'color' => 'success',
            ],
            75 => [
                'label' => '75% Tuition Fee Discount',
                'color' => 'info',
            ],
            50 => [
                'label' => '50% Tuition Fee Discount',
                'color' => 'info',
            ],
            25 => [
                'label' => '25% Tuition Fee Discount',
                'color' => 'warning',
            ],
            10 => [
                'label' => '10% Tuition Fee Discount',
                'color' => 'warning',
            ],
            default => [
                'label' => 'No Discount',
                'color' => 'danger',
            ],
        };
    }
}