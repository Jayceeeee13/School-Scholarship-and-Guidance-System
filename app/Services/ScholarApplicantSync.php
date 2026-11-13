<?php

namespace App\Services;

use App\Models\Applicant;
use App\Models\Gender;
use App\Models\InstitutionalScholar;
use App\Models\Program;
use App\Models\Scholars;
use App\Models\Students;
use App\Models\TypeOfScholarship;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

/**
 * Keeps an approved Applicant and its scholar records
 * (institutional_scholars + scholars) in sync, in both directions.
 *
 * Call fromApplicant() after an applicant is edited, or
 * fromScholarRecord() after an InstitutionalScholar / Scholars row is edited.
 * Pass the record's raw attributes from BEFORE the save so the counterpart
 * can still be found if the name or birthdate was changed.
 *
 * Only fields that actually changed are copied. The other records are
 * saved quietly (no model events) so the sync can never loop.
 */
class ScholarApplicantSync
{
    /** Fields kept in sync, named the way the scholar tables name them. */
    protected const FIELDS = [
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'birthdate',
        'sex',
        'program',
        'year_level',
        'type_of_scholarship',
        'benefit',
    ];

    // ─────────────────────────────────────────────────────────────
    // Entry points
    // ─────────────────────────────────────────────────────────────

    public static function fromApplicant(Applicant $applicant, array $oldRaw): ?array
    {
        // Only approved applicants have scholar records.
        if ($applicant->status !== 'approved') {
            return null;
        }

        return static::run(
            $applicant,
            $oldRaw,
            static::applicantToScholarSpace($oldRaw),
            static::applicantToScholarSpace($applicant->getAttributes())
        );
    }

    /**
     * @param  Model  $record  InstitutionalScholar or Scholars (already saved)
     */
    public static function fromScholarRecord(Model $record, array $oldRaw): ?array
    {
        return static::run(
            $record,
            $oldRaw,
            static::pick($oldRaw),
            static::pick($record->getAttributes())
        );
    }

    /**
     * Shows the result of a sync to the user (Filament notifications).
     */
    public static function notify(?array $result): void
    {
        if (! $result) {
            return;
        }

        if (! empty($result['updated'])) {
            $body = 'Also updated: ' . implode(', ', $result['updated']) . '.';

            if (! empty($result['slot_note'])) {
                $body .= ' ' . $result['slot_note'];
            }

            Notification::make()
                ->title('Linked records updated too')
                ->body($body)
                ->success()
                ->send();
        }

        foreach ($result['warnings'] ?? [] as $warning) {
            Notification::make()
                ->title('Sync notice')
                ->body($warning)
                ->warning()
                ->persistent()
                ->send();
        }
    }

    /**
     * One-line summary for the activity log.
     */
    public static function describe(array $result): string
    {
        return 'Synced ' . implode(', ', $result['changed'])
            . ' to linked records: ' . implode(', ', $result['updated']);
    }

    // ─────────────────────────────────────────────────────────────
    // Core
    // ─────────────────────────────────────────────────────────────

    protected static function run(Model $origin, array $oldRaw, array $oldSpace, array $newSpace): ?array
    {
        // ── 1. Which fields really changed ───────────────────────────
        $changes = [];

        foreach (self::FIELDS as $field) {
            $new = $newSpace[$field] ?? null;

            // A failed name lookup must never blank a field on the other side.
            if (in_array($field, ['sex', 'program', 'type_of_scholarship'], true) && $new === null) {
                continue;
            }

            if ($field === 'birthdate' && $new) {
                $new = Carbon::parse($new)->toDateString();
            }

            if (static::valueChanged($field, $oldSpace[$field] ?? null, $new)) {
                $changes[$field] = $new;
            }
        }

        if (empty($changes)) {
            return null;
        }

        // ── 2. Find the other records ────────────────────────────────
        $oldKeys = static::keysFrom($oldRaw, $origin);
        $newKeys = static::keysFrom($origin->getAttributes(), $origin);

        $applicant = $origin instanceof Applicant
            ? $origin
            : static::findApplicant([$oldKeys, $newKeys]);

        $institutional = $origin instanceof InstitutionalScholar
            ? $origin
            : static::find(InstitutionalScholar::class, [$oldKeys, $newKeys]);

        if ($origin instanceof Scholars) {
            $scholar = $origin;
        } else {
            $lookup = [$oldKeys, $newKeys];

            // Scholars rows are matched the same way ListScholars::findMatchingScholar()
            // does it: by the institutional record's own keys, scoped to its term.
            if ($institutional && ! ($origin instanceof InstitutionalScholar)) {
                array_unshift($lookup, static::keysFrom($institutional->getAttributes(), $institutional));
            }

            $scholar = static::find(Scholars::class, $lookup);
        }

        $warnings  = [];
        $slotNote  = null;
        $slotMoved = false;

        // ── 3. Move scholarship slots when the type changed ──────────
        // Slots are only consumed when an applicant is approved, so they are
        // only moved when an approved applicant is part of this link. A revoked
        // scholar has already given their slot back.
        if (isset($changes['type_of_scholarship']) && $applicant) {
            $revoked = collect([$origin, $institutional, $scholar])
                ->filter()
                ->contains(fn ($m) => ($m->status ?? null) === 'revoked');

            if (! $revoked) {
                $oldName = $oldSpace['type_of_scholarship'] ?? null;
                $newName = $changes['type_of_scholarship'];

                $oldType = $oldName ? TypeOfScholarship::where('name', $oldName)->first() : null;
                $newType = TypeOfScholarship::where('name', $newName)->first();

                if (! $newType) {
                    $warnings[] = "'{$newName}' is not a registered Type of Scholarship, so no slots were changed.";
                } else {
                    $oldType?->increment('slots');

                    if ($newType->slots > 0) {
                        $newType->decrement('slots');
                        $slotMoved = true;
                        $slotNote  = 'Slots moved from ' . ($oldName ?: 'the previous type') . " to {$newName}.";
                    } else {
                        $warnings[] = "{$newName} had no slots left. The type was changed anyway, but its slots were not reduced.";
                    }
                }
            }
        }

        // ── 4. Apply the changes to the other records ────────────────
        $updated = [];

        if ($institutional && $institutional !== $origin) {
            $institutional->forceFill($changes)->saveQuietly();
            $updated[] = 'institutional scholar';
        }

        if ($scholar && $scholar !== $origin) {
            $scholar->forceFill($changes)->saveQuietly();
            $updated[] = 'scholar record';
        }

        if ($applicant && $applicant !== $origin) {
            $applicantChanges = static::toApplicantSpace($changes, $warnings);

            if (! empty($applicantChanges)) {
                $applicant->forceFill($applicantChanges)->saveQuietly();
                $updated[] = 'applicant';
            }
        }

        if (empty($updated) && ! $slotMoved && empty($warnings)) {
            return null;
        }

        return [
            'changed'   => array_keys($changes),
            'updated'   => $updated,
            'warnings'  => $warnings,
            'slot_note' => $slotNote,
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Finding the matching records
    // ─────────────────────────────────────────────────────────────

    /**
     * The values used to match a person across the three tables.
     * Applicants have no scholar term, so term_id is left out for them.
     */
    protected static function keysFrom(array $raw, Model $model): array
    {
        return [
            'user_id'    => $raw['user_id'] ?? null,
            'student_id' => $raw['student_id'] ?? null,
            'first_name' => $raw['first_name'] ?? null,
            'last_name'  => $raw['last_name'] ?? null,
            'birthdate'  => ! empty($raw['birthdate'])
                ? Carbon::parse($raw['birthdate'])->toDateString()
                : null,
            'term_id'    => $model instanceof Applicant ? null : ($raw['term_id'] ?? null),
        ];
    }

    /**
     * Finds a scholar row: user_id first, then student_id, then name + birthdate.
     * Scoped to the term when one is known. The newest match wins.
     */
    protected static function find(string $class, array $keysList): ?Model
    {
        foreach ($keysList as $k) {
            $scopes = [];

            if (! empty($k['user_id'])) {
                $scopes[] = fn ($q) => $q->where('user_id', $k['user_id']);
            }

            if (! empty($k['student_id'])) {
                $scopes[] = fn ($q) => $q->where('student_id', $k['student_id']);
            }

            if (! empty($k['first_name']) && ! empty($k['last_name']) && ! empty($k['birthdate'])) {
                $scopes[] = fn ($q) => $q->where('first_name', $k['first_name'])
                    ->where('last_name', $k['last_name'])
                    ->whereDate('birthdate', $k['birthdate']);
            }

            foreach ($scopes as $scope) {
                $query = $class::query();
                $scope($query);

                if (! empty($k['term_id'])) {
                    $query->where('term_id', $k['term_id']);
                }

                if ($found = $query->latest('id')->first()) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Finds the newest approved, non-archived applicant for a person.
     * Applicants have no student_id column, so it is turned into a user_id first.
     */
    protected static function findApplicant(array $keysList): ?Applicant
    {
        foreach ($keysList as $k) {
            if (empty($k['user_id']) && ! empty($k['student_id'])) {
                $k['user_id'] = Students::where('student_id', $k['student_id'])->value('user_id');
            }

            $scopes = [];

            if (! empty($k['user_id'])) {
                $scopes[] = fn ($q) => $q->where('user_id', $k['user_id']);
            }

            if (! empty($k['first_name']) && ! empty($k['last_name']) && ! empty($k['birthdate'])) {
                $scopes[] = fn ($q) => $q->where('first_name', $k['first_name'])
                    ->where('last_name', $k['last_name'])
                    ->whereDate('birthdate', $k['birthdate']);
            }

            foreach ($scopes as $scope) {
                $query = Applicant::query()
                    ->where('status', 'approved')
                    ->whereNull('archived_at');

                $scope($query);

                if ($found = $query->latest('id')->first()) {
                    return $found;
                }
            }
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────
    // Translating between applicant columns and scholar columns
    // ─────────────────────────────────────────────────────────────

    /**
     * Applicant raw attributes -> scholar column names
     * (gender / program / scholarship type ids become their names).
     */
    protected static function applicantToScholarSpace(array $raw): array
    {
        return [
            'first_name'          => $raw['first_name'] ?? null,
            'middle_name'         => $raw['middle_name'] ?? null,
            'last_name'           => $raw['last_name'] ?? null,
            'extension_name'      => $raw['extension_name'] ?? null,
            'birthdate'           => $raw['birthdate'] ?? null,
            'year_level'          => $raw['year_level'] ?? null,
            'benefit'             => $raw['benefit'] ?? null,
            'sex'                 => ! empty($raw['gender_id'])
                ? Gender::find($raw['gender_id'])?->name
                : null,
            'program'             => ! empty($raw['program_id'])
                ? Program::find($raw['program_id'])?->name
                : null,
            'type_of_scholarship' => ! empty($raw['type_of_scholarship_id'])
                ? TypeOfScholarship::find($raw['type_of_scholarship_id'])?->name
                : null,
        ];
    }

    /**
     * Scholar-style changes -> applicant columns (names become ids).
     * Anything that cannot be matched is skipped and reported in $warnings.
     */
    protected static function toApplicantSpace(array $changes, array &$warnings): array
    {
        $out = [];

        foreach (['first_name', 'middle_name', 'last_name', 'extension_name', 'year_level', 'benefit'] as $field) {
            if (array_key_exists($field, $changes)) {
                $out[$field] = $changes[$field];
            }
        }

        if (array_key_exists('birthdate', $changes)) {
            $out['birthdate'] = $changes['birthdate'];
            $out['age']       = $changes['birthdate']
                ? Carbon::parse($changes['birthdate'])->age
                : null;
        }

        if (isset($changes['sex'])) {
            $id = Gender::where('name', $changes['sex'])->value('id');

            if ($id) {
                $out['gender_id'] = $id;
            } else {
                $warnings[] = "Sex '{$changes['sex']}' does not match a Gender, so the applicant's gender was not changed.";
            }
        }

        if (isset($changes['program'])) {
            $id = Program::where('name', $changes['program'])->value('id');

            if ($id) {
                $out['program_id'] = $id;
            } else {
                $warnings[] = "Program '{$changes['program']}' does not match a registered Program, so the applicant's program was not changed.";
            }
        }

        if (isset($changes['type_of_scholarship'])) {
            $id = TypeOfScholarship::where('name', $changes['type_of_scholarship'])->value('id');

            if ($id) {
                $out['type_of_scholarship_id'] = $id;
            }
            // No match: run() already warned about it while handling slots.
        }

        return $out;
    }

    protected static function pick(array $raw): array
    {
        return array_intersect_key($raw, array_flip(self::FIELDS));
    }

    /**
     * True when two values really differ (null and '' are the same,
     * 80 / "80" / "80.00" are the same, dates compare by day only).
     */
    protected static function valueChanged(string $field, $old, $new): bool
    {
        if ($field === 'birthdate') {
            $oldDay = $old ? Carbon::parse($old)->toDateString() : null;
            $newDay = $new ? Carbon::parse($new)->toDateString() : null;

            return $oldDay !== $newDay;
        }

        $old = ($old === '' || $old === null) ? null : $old;
        $new = ($new === '' || $new === null) ? null : $new;

        if (is_numeric($old) && is_numeric($new)) {
            return (float) $old !== (float) $new;
        }

        return (string) $old !== (string) $new;
    }
}