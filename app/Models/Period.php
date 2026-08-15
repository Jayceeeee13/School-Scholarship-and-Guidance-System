<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = ['key', 'label', 'is_open', 'open_date', 'close_date'];

    protected $casts = [
        'open_date'  => 'datetime',
        'close_date' => 'datetime',
    ];

    // ── Generic finder ────────────────────────────────────────────────────

    public static function get(string $key): self
    {
        return static::firstOrCreate(
            ['key' => $key],
            ['label' => ucwords(str_replace('_', ' ', $key)), 'is_open' => false]
        );
    }

    // ── Shorthand helpers ─────────────────────────────────────────────────

    public static function scholarshipApplication(): self
    {
        return static::get('scholarship_application');
    }

    public static function scholarshipRequirement(): self
    {
        return static::get('scholarship_requirement');
    }

    public static function exam(): self
    {
        return static::get('exam');
    }

    // ── Aliases (for backward compatibility) ─────────────────────────────

    public static function application(): self
    {
        return static::scholarshipApplication();
    }

    public static function requirement(): self
    {
        return static::scholarshipRequirement();
    }

    // ── Auto open/close based on schedule ────────────────────────────────

    public function getIsOpenAttribute(): bool
    {
        $now = now();

        if (!$this->open_date && !$this->close_date) {
            return (bool) $this->attributes['is_open'];
        }

        if ($this->open_date && !$this->close_date) {
            return $now->gte($this->open_date);
        }

        if ($this->open_date && $this->close_date) {
            return $now->between($this->open_date, $this->close_date);
        }

        return (bool) $this->attributes['is_open'];
    }

    // ── Display helpers ───────────────────────────────────────────────────

    public function opensOnLabel(): ?string
    {
        return $this->open_date?->format('F d, Y h:i A');
    }

    public function closesOnLabel(): ?string
    {
        return $this->close_date?->format('F d, Y h:i A');
    }
}