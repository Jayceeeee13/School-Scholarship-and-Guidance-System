<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use App\Models\Students;
use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsAllActivity;

    protected $fillable = [
        'avatar',
        'name',
        'last_name',
        'email',
        'password',
        'birthdate',
        'address',
        'contact_no',
        'gender_id',
        'personnel_id',
        'role_id',
        'department_id',
        'archived_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'archived_at'       => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnels::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Students::class, 'user_id');
    }

    public function applicant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Applicant::class);
    }

    public function examResults()
    {
        return $this->hasMany(\App\Models\ExamAttempt::class, 'applicant_id');
    }

    public function assignedScholars(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Scholars::class, 'department_head_id');
    }

    // ── Archive scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    // ── Filament ─────────────────────────────────────────────────────

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->isArchived()) {
            return false;
        }

        return in_array(strtolower($this->role?->name), ['admin', 'guidance', 'scholarship', 'department head']);
    }

    // ── Role helpers ─────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role && strtolower($this->role->name) === 'admin';
    }

    public function isGuidance(): bool
    {
        return $this->role && strtolower($this->role->name) === 'guidance';
    }

    public function isScholarship(): bool
    {
        return $this->role && strtolower($this->role->name) === 'scholarship';
    }

    public function isDepartmentHead(): bool
    {
        return $this->role && strtolower($this->role->name) === 'department head';
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && strtolower($this->role->name) === strtolower($roleName);
    }

    public function hasAnyRole(array $roleNames): bool
    {
        if (!$this->role) return false;
        return in_array(strtolower($this->role->name), array_map('strtolower', $roleNames));
    }

    public function getRoleName(): ?string
    {
        return $this->role?->name;
    }

    public function isEnrolled(): bool
    {
        return $this->student()->exists();
    }

    public function hasApplied(): bool
    {
        return $this->applicant()->exists();
    }
}