<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
        'passing_score',
        'is_active',
        'access_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * True when the user has a finished attempt whose score reaches the
     * passing score. `passing_score` is compared against the attempt's
     * percentage (0–100). If no passing score is set, any finished attempt
     * counts as a pass.
     */
    public function hasBeenPassedBy(int $userId): bool
    {
        $attempt = ExamAttempt::latestAttempt($userId, $this->id);

        if (! $attempt || is_null($attempt->completed_at)) {
            return false;
        }

        if (is_null($this->passing_score)) {
            return true;
        }

        return (float) $attempt->percentage >= (float) $this->passing_score;
    }

    /** 6-character code, without look-alike characters (0/O, 1/I). */
    public static function generateAccessCode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (static::where('access_code', $code)->exists());

        return $code;
    }
}