<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use App\Models\Students;
use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /** Legacy "primary" role (kept so old code keeps working). */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /** All roles assigned to this user. */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
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

        return $this->hasAnyRole(['admin', 'guidance', 'scholarship', 'department head']);
    }

    // ── Role helpers ─────────────────────────────────────────────────

    /**
     * Lower-cased names of every role the user has
     * (pivot roles + legacy role_id, de-duplicated).
     */
    public function roleNames(): array
    {
        return $this->roles
            ->pluck('name')
            ->push($this->role?->name)
            ->filter()
            ->map(fn ($name) => strtolower($name))
            ->unique()
            ->values()
            ->all();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isGuidance(): bool
    {
        return $this->hasRole('guidance');
    }

    public function isScholarship(): bool
    {
        return $this->hasRole('scholarship');
    }

    public function isDepartmentHead(): bool
    {
        return $this->hasRole('department head');
    }

    public function hasRole(string $roleName): bool
    {
        return in_array(strtolower($roleName), $this->roleNames(), true);
    }

    public function hasAnyRole(array $roleNames): bool
    {
        return count(array_intersect(
            array_map('strtolower', $roleNames),
            $this->roleNames()
        )) > 0;
    }

    /** Primary role name (first role), kept for backward compatibility. */
    public function getRoleName(): ?string
    {
        return $this->roles->first()?->name ?? $this->role?->name;
    }

    /** Comma-separated list of all role names. */
    public function getRoleNames(): string
    {
        return $this->roles->pluck('name')->implode(', ');
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