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

    /**
     * Human-readable names for each violation type recorded by exam/show.blade.php.
     * ExamController::recordViolation() only accepts the keys listed here.
     */
    public const VIOLATION_LABELS = [
        'tab_switch'         => 'Tab Switching',
        'window_blur'        => 'Window Blur (clicked outside / switched app)',
        'fullscreen_exit'    => 'Exited Fullscreen',
        'screenshot_attempt' => 'Screenshot Attempt',
        'copy_attempt'       => 'Copy Attempt',
        'paste_attempt'      => 'Paste Attempt',
        'cut_attempt'        => 'Cut Attempt',
        'right_click'        => 'Right Click',
        'devtools_attempt'   => 'Dev Tools Attempt',
        'print_attempt'      => 'Print Attempt',
    ];

    // ── Auto-set scholarship type to Academic once an exam is finished ────

    protected static function booted(): void
    {
        static::saved(function (ExamAttempt $attempt) {
            // An exam counts as finished once completed_at is filled in.
            if (is_null($attempt->completed_at)) {
                return;
            }

            $academicId = static::academicScholarshipId();

            if (! $academicId) {
                return;
            }

            // Loop (instead of a bulk update) so the Applicant's activity
            // logging trait still fires for each change.
            Applicant::query()
                ->where('user_id', $attempt->user_id)
                ->where(function ($q) use ($academicId) {
                    $q->whereNull('type_of_scholarship_id')
                      ->orWhere('type_of_scholarship_id', '!=', $academicId);
                })
                ->get()
                ->each(fn (Applicant $applicant) => $applicant->update([
                    'type_of_scholarship_id' => $academicId,
                ]));
        });
    }

    /**
     * ID of the "Academic" scholarship type (matched by name).
     */
    public static function academicScholarshipId(): ?int
    {
        return TypeOfScholarship::query()
            ->where('name', 'like', '%academic%')
            ->value('id');
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Answers in the same order the questions were shown to the examinee.
     * Older records have no position, so they fall back to id order.
     */
    public function answers()
    {
        return $this->hasMany(ExamAnswer::class)
            ->orderBy('position')
            ->orderBy('id');
    }

    // ── Violations & duration helpers ──────────────────────────────────────

    public static function violationLabel(?string $type): string
    {
        return self::VIOLATION_LABELS[$type] ?? ucfirst(str_replace('_', ' ', (string) $type));
    }

    /** e.g. "1h 12m 05s" / "45m 10s", or "—" when no real duration was recorded. */
    public function getDurationLabelAttribute(): string
    {
        if (! $this->started_at || ! $this->completed_at) {
            return '—';
        }

        $seconds = (int) abs($this->completed_at->diffInSeconds($this->started_at));

        // Old attempts had started_at == completed_at, so no duration exists
        if ($seconds === 0) {
            return '—';
        }

        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        return $h > 0 ? "{$h}h {$m}m {$s}s" : "{$m}m {$s}s";
    }

    /**
     * Count per violation type, most frequent first.
     * e.g. ['Tab Switching × 3', 'Screenshot Attempt × 1']
     */
    public function getViolationSummaryAttribute(): array
    {
        return collect($this->violations ?? [])
            ->groupBy(fn ($v) => $v['type'] ?? 'unknown')
            ->map(fn ($items, $type) => [
                'label' => self::violationLabel($type),
                'count' => $items->count(),
            ])
            ->sortByDesc('count')
            ->map(fn ($row) => "{$row['label']} × {$row['count']}")
            ->values()
            ->all();
    }

    /** One readable line per recorded violation, with time and time left. */
    public function getViolationLogAttribute(): array
    {
        return collect($this->violations ?? [])->map(function ($v) {
            $label = self::violationLabel($v['type'] ?? null);

            $at = isset($v['time'])
                ? \Carbon\Carbon::parse($v['time'])->timezone(config('app.timezone'))->format('h:i:s A')
                : null;

            $left = isset($v['timeLeft'])
                ? sprintf('%02d:%02d left', intdiv((int) $v['timeLeft'], 60), (int) $v['timeLeft'] % 60)
                : null;

            return trim($label . ($at ? " — {$at}" : '') . ($left ? " ({$left})" : ''));
        })->all();
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
     * Raw percentage -> integer discount code stored in `discount`.
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
     * Stored discount code -> label + Filament badge colour.
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