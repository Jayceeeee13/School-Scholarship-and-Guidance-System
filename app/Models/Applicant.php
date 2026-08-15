<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Applicant extends Model
{
    use LogsAllActivity;

    protected $fillable = [
        'user_id',
        'picture',
        'type_of_application_id',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'gender_id',
        'birthdate',
        'age',
        'contact_no',
        'program_id',
        'year_level',
        'religion',
        'facebook_account',
        'fathers_name',
        'fathers_contact_no',
        'mothers_name',
        'mothers_contact_no',
        'guardian',
        'guardian_contact_no',
        'type_of_scholarship_id',
        'status',
        'interview',
        'benefit',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    /**
     * The user who submitted this application.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function typeOfApplication()
    {
        return $this->belongsTo(TypeOfApplication::class);
    }

    public function typeOfScholarship()
    {
        return $this->belongsTo(TypeOfScholarship::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function submittedRequirements(): BelongsToMany
    {
        return $this->belongsToMany(Requirement::class, 'applicant_requirement')
            ->withPivot('is_submitted', 'file_path', 'notes')
            ->withTimestamps();
    }

    /**
     * Returns the latest completed exam attempt for this applicant's user.
     * Useful for reading the most up-to-date discount at any point.
     */
    public function latestExamAttempt()
    {
        return $this->hasOneThrough(
            ExamAttempt::class,
            User::class,
            'id',       // users.id
            'user_id',  // exam_attempts.user_id
            'user_id',  // applicants.user_id
            'id'        // users.id
        )
        ->whereIn('status', ['completed', 'submitted'])
        ->latest('completed_at');
    }

    // ── Archive scopes ───────────────────────────────────────────────────

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

    // ── Helpers ──────────────────────────────────────────────────────────

    public function hasCompleteRequirements(): bool
    {
        $total = \App\Models\Requirement::where('type_of_application_id', $this->type_of_application_id)->count();
        if ($total === 0) return false;
        $submitted = $this->submittedRequirements()->wherePivot('is_submitted', true)->count();
        return $submitted >= $total;
    }

    // ── Accessors ────────────────────────────────────────────────────────

    public function getPictureUrlAttribute()
    {
        if (! $this->picture) {
            return asset('images/default-avatar.png');
        }

        return asset('storage/' . $this->picture);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}