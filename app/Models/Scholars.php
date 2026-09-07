<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use App\Traits\HasScholarProfile;
use Illuminate\Database\Eloquent\Model;

class Scholars extends Model
{
    use LogsAllActivity, HasScholarProfile;

    // Scholarship types allowed to submit accomplishment reports
    public const ACCOMPLISHMENT_ELIGIBLE_TYPES = [
        'Talents',
        'Supreme Student Government',
        'Sports',
    ];

    protected $fillable = [
        'student_id',
        'department_head_id',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'sex',
        'birthdate',
        'program',
        'year_level',
        'type_of_scholarship',
        'batch_no',
        'ip_group',
        'pwd',
        'benefit',
        'status',
        'term_id',
        'revocation_reason',
        'revoked_at',
    ];

    protected $casts = [
        'birthdate'  => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    // ── Auto-link a newly created scholar to a matching login account, ──
    // if one already exists and no user_id was explicitly set at
    // creation time. This only fires on CREATE, so it does not
    // retroactively fix scholars already sitting in the table with a
    // NULL user_id — those need a one-time manual UPDATE.
    //
    // This is Scholars-specific behavior (not shared via the trait)
    // since InstitutionalScholar records may be linked differently
    // (e.g. explicitly set during applicant approval).
    protected static function booted(): void
    {
        static::created(function (Scholars $scholar) {
            if ($scholar->user_id) {
                return; // already linked explicitly at creation
            }

            $user = User::whereRaw('LOWER(last_name) = ?', [strtolower($scholar->last_name)])
                ->whereDate('birthdate', $scholar->birthdate)
                ->first();

            if ($user) {
                $scholar->update(['user_id' => $user->id]);
            }
        });

        // ── Keep institutional_scholars in sync ──────────────────────────
        // Whenever a Scholars row is created or updated with a
        // type_of_scholarship matching a registered TypeOfScholarship
        // name, mirror it into the institutional_scholars table so the
        // "Institutional Scholars" tab (which reads institutional_scholars
        // exclusively) stays complete. Matched by name + birthdate so
        // repeated saves update the same mirrored row instead of
        // duplicating it.
        static::saved(function (Scholars $scholar) {
            $institutionalTypes = \App\Models\TypeOfScholarship::pluck('name')->toArray();

            $isInstitutional = collect($institutionalTypes)->contains(
                fn ($name) => strcasecmp(trim($scholar->type_of_scholarship ?? ''), trim($name)) === 0
            );

            if (! $isInstitutional) {
                return;
            }

            \App\Models\InstitutionalScholar::updateOrCreate(
                [
                    'first_name' => $scholar->first_name,
                    'last_name'  => $scholar->last_name,
                    'birthdate'  => $scholar->birthdate,
                ],
                [
                    'student_id'          => $scholar->student_id,
                    'user_id'             => $scholar->user_id,
                    'middle_name'         => $scholar->middle_name,
                    'extension_name'      => $scholar->extension_name,
                    'sex'                 => $scholar->sex,
                    'program'             => $scholar->program,
                    'year_level'          => $scholar->year_level,
                    'type_of_scholarship' => $scholar->type_of_scholarship,
                    'batch_no'            => $scholar->batch_no,
                    'ip_group'            => $scholar->ip_group,
                    'pwd'                 => $scholar->pwd,
                    'benefit'             => $scholar->benefit,
                    'status'              => $scholar->status,
                    'term_id'             => $scholar->term_id,
                ]
            );
        });
    }

    // ── departmentHead(), dailyTimeRecords(), term(), user(),
    // isEligibleForAccomplishmentReports(), forUser(), normalizeName(),
    // getFullNameAttribute(), scopeActive/Inactive/Revoked/NotRevoked,
    // scopeByBatch, isRevoked(), generateStudentId()) now comes from
    // App\Traits\HasScholarProfile — kept identical to InstitutionalScholar
    // so both models behave consistently. ──────────────────────────────
}