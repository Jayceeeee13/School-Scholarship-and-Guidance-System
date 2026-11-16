<?php

namespace App\Services;

use App\Filament\Resources\ScholarsResource\Pages\ListScholars;
use App\Models\Applicant;
use App\Models\Gender;
use App\Models\InstitutionalScholar;
use App\Models\Program;
use App\Models\Scholars;
use App\Models\TypeOfScholarship;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Keeps an approved Applicant, its Institutional Scholar record and its
 * Scholars record in step with each other, in BOTH directions.
 *
 * - Only fields that actually changed are copied.
 * - Counterpart records are saved "quietly" (no model events), so an edit
 *   on one side can never bounce back and trigger the other side again.
 * - Changing the type of scholarship moves one slot: +1 to the old type,
 *   -1 to the new type. The move is refused when the new type is full.
 *
 * Field map
 *   applicants                scholars / institutional_scholars
 *   first/middle/last/ext     same names
 *   year_level, benefit       same names
 *   birthdate                 same name (applicant age is recomputed)
 *   gender_id                 sex                  (stored as the NAME)
 *   program_id                program              (stored as the NAME)
 *   type_of_scholarship_id    type_of_scholarship  (stored as the NAME)
 */
class ScholarSyncService
{
    /** Fields that have the same name and meaning on both sides. */
    protected const SHARED = [
        'first_name', 'middle_name', 'last_name', 'extension_name',
        'year_level', 'benefit', 'birthdate',
    ];

    // ═════════════════════════════════════════════════════════════════════
    //  APPLICANT  →  INSTITUTIONAL SCHOLAR + SCHOLARS
    // ═════════════════════════════════════════════════════════════════════

    /**
     * Call AFTER an applicant was saved.
     *
     * @param  array  $old  The applicant's raw attributes BEFORE the save.
     * @return array{changes:array, institutional:?Model, scholar:?Model, slots:bool}|null
     */
    public static function syncFromApplicant(Applicant $applicant, array $old): ?array
    {
        if ($applicant->status !== 'approved' || empty($old)) {
            return null;
        }

        $new      = $applicant->getAttributes();
        $changes  = [];
        $slotMove = null;

        foreach (self::SHARED as $field) {
            if (self::valueChanged($field, $old[$field] ?? null, $new[$field] ?? null)) {
                $changes[$field] = $field === 'birthdate'
                    ? (! empty($new[$field]) ? Carbon::parse($new[$field])->toDateString() : null)
                    : ($new[$field] ?? null);
            }
        }

        if (self::valueChanged('gender_id', $old['gender_id'] ?? null, $new['gender_id'] ?? null)) {
            if ($sex = Gender::find($new['gender_id'] ?? null)?->name) {
                $changes['sex'] = $sex;
            }
        }

        if (self::valueChanged('program_id', $old['program_id'] ?? null, $new['program_id'] ?? null)) {
            if ($program = Program::find($new['program_id'] ?? null)?->name) {
                $changes['program'] = $program;
            }
        }

        if (self::valueChanged(
            'type_of_scholarship_id',
            $old['type_of_scholarship_id'] ?? null,
            $new['type_of_scholarship_id'] ?? null
        )) {
            if ($type = TypeOfScholarship::find($new['type_of_scholarship_id'] ?? null)) {
                $changes['type_of_scholarship'] = $type->name;
                $slotMove = [$old['type_of_scholarship_id'] ?? null, $type->id];
            }
        }

        if (empty($changes)) {
            return null;
        }

        // Find the scholar records using the OLD values.
        $institutional = self::findInstitutionalForApplicant($old);

        if (! $institutional) {
            return null;
        }

        $scholar = ListScholars::findMatchingScholar($institutional);

        DB::transaction(function () use ($institutional, $scholar, $changes, $slotMove) {
            self::quietUpdate($institutional, $changes);

            if ($scholar) {
                self::quietUpdate($scholar, $changes);
            }

            if ($slotMove) {
                self::moveSlot($slotMove[0], $slotMove[1]);
            }
        });

        return [
            'changes'       => $changes,
            'institutional' => $institutional,
            'scholar'       => $scholar,
            'slots'         => (bool) $slotMove,
        ];
    }

    /**
     * Call in mutateFormDataBeforeSave() of the applicant edit page.
     * Returns an error message when the new type of scholarship has no slots
     * left (and the applicant really is linked to a scholar), otherwise null.
     */
    public static function ensureSlotForApplicantTypeChange(Applicant $record, array $data): ?string
    {
        $old = $record->getRawOriginal();

        if ($record->status !== 'approved') {
            return null;
        }

        if (! self::valueChanged(
            'type_of_scholarship_id',
            $old['type_of_scholarship_id'] ?? null,
            $data['type_of_scholarship_id'] ?? null
        )) {
            return null;
        }

        $type = TypeOfScholarship::find($data['type_of_scholarship_id'] ?? null);

        if (! $type || ! self::findInstitutionalForApplicant($old)) {
            return null;
        }

        return $type->slots > 0
            ? null
            : "There are no remaining slots for {$type->name}.";
    }

    // ═════════════════════════════════════════════════════════════════════
    //  SCHOLARS / INSTITUTIONAL SCHOLAR  →  APPLICANT (+ the other scholar table)
    // ═════════════════════════════════════════════════════════════════════

    /**
     * Call AFTER a Scholars or InstitutionalScholar record was saved.
     *
     * @param  Model  $source  The scholar record that was just edited.
     * @param  array  $old     Its raw attributes BEFORE the save.
     * @return array{changes:array, other:?Model, applicant:?Applicant, slots:bool}|null
     */
    public static function syncFromScholarRecord(Model $source, array $old): ?array
    {
        $new     = $source->getAttributes();
        $changes = [];

        foreach (array_merge(self::SHARED, ['sex', 'program', 'type_of_scholarship']) as $field) {
            if (self::valueChanged($field, $old[$field] ?? null, $new[$field] ?? null)) {
                $changes[$field] = $field === 'birthdate'
                    ? (! empty($new[$field]) ? Carbon::parse($new[$field])->toDateString() : null)
                    : ($new[$field] ?? null);
            }
        }

        if (empty($changes)) {
            return null;
        }

        // ── Find the counterparts using the OLD values ──────────────────
        if ($source instanceof InstitutionalScholar) {
            $probe = new InstitutionalScholar();
            $probe->setRawAttributes($old);
            $other = ListScholars::findMatchingScholar($probe);
        } else {
            $other = self::findInstitutionalForScholar($old);
        }

        $applicant = self::findApplicantForScholar($old);

        // ── Translate the scholar fields into applicant columns ─────────
        $applicantChanges = [];
        $slotMove         = null;

        foreach ($changes as $field => $value) {
            switch ($field) {
                case 'sex':
                    if ($id = Gender::where('name', $value)->value('id')) {
                        $applicantChanges['gender_id'] = $id;
                    }
                    break;

                case 'program':
                    if ($id = Program::where('name', $value)->value('id')) {
                        $applicantChanges['program_id'] = $id;
                    }
                    break;

                case 'type_of_scholarship':
                    if ($newType = TypeOfScholarship::where('name', $value)->first()) {
                        $applicantChanges['type_of_scholarship_id'] = $newType->id;
                        $oldTypeId = TypeOfScholarship::where('name', $old['type_of_scholarship'] ?? null)->value('id');
                        $slotMove  = [$oldTypeId, $newType->id];
                    }
                    break;

                case 'birthdate':
                    $applicantChanges['birthdate'] = $value;
                    $applicantChanges['age']       = $value ? Carbon::parse($value)->age : null;
                    break;

                default:
                    $applicantChanges[$field] = $value;
            }
        }

        DB::transaction(function () use ($other, $applicant, $changes, $applicantChanges, $slotMove) {
            if ($other) {
                self::quietUpdate($other, $changes);
            }

            if ($applicant) {
                if ($applicantChanges) {
                    self::quietUpdate($applicant, $applicantChanges);
                }

                // The slot was used when the applicant was approved, so it is
                // only moved when the scholar is linked to an applicant.
                if ($slotMove) {
                    self::moveSlot($slotMove[0], $slotMove[1]);
                }
            }
        });

        return [
            'changes'   => $changes,
            'other'     => $other,
            'applicant' => $applicant,
            'slots'     => $applicant && $slotMove,
        ];
    }

    /**
     * Call BEFORE saving a scholar edit. Returns an error message when the
     * new type of scholarship is full (and the scholar is linked to an
     * applicant), otherwise null.
     *
     * @param  array  $old   Raw attributes before the edit.
     * @param  array  $data  Submitted form data.
     */
    public static function ensureSlotForScholarTypeChange(array $old, array $data): ?string
    {
        $newName = $data['type_of_scholarship'] ?? null;

        if (! self::valueChanged('type_of_scholarship', $old['type_of_scholarship'] ?? null, $newName)) {
            return null;
        }

        $type = TypeOfScholarship::where('name', $newName)->first();

        if (! $type || ! self::findApplicantForScholar($old)) {
            return null;
        }

        return $type->slots > 0
            ? null
            : "There are no remaining slots for {$type->name}.";
    }

    // ═════════════════════════════════════════════════════════════════════
    //  Finders
    // ═════════════════════════════════════════════════════════════════════

    protected static function findInstitutionalForApplicant(array $old): ?InstitutionalScholar
    {
        if (! empty($old['user_id'])) {
            $found = InstitutionalScholar::where('user_id', $old['user_id'])->latest('id')->first();

            if ($found) {
                return $found;
            }
        }

        if (! empty($old['birthdate'])) {
            return InstitutionalScholar::where('first_name', $old['first_name'] ?? null)
                ->where('last_name', $old['last_name'] ?? null)
                ->whereDate('birthdate', Carbon::parse($old['birthdate'])->toDateString())
                ->latest('id')
                ->first();
        }

        return null;
    }

    protected static function findInstitutionalForScholar(array $old): ?InstitutionalScholar
    {
        $query = InstitutionalScholar::query();

        if (! empty($old['user_id'])) {
            $query->where('user_id', $old['user_id']);
        } elseif (! empty($old['student_id'])) {
            $query->where('student_id', $old['student_id']);
        } elseif (! empty($old['birthdate'])) {
            $query->where('first_name', $old['first_name'] ?? null)
                ->where('last_name', $old['last_name'] ?? null)
                ->whereDate('birthdate', Carbon::parse($old['birthdate'])->toDateString());
        } else {
            return null;
        }

        if (! empty($old['term_id'])) {
            $query->where('term_id', $old['term_id']);
        }

        return $query->latest('id')->first();
    }

    protected static function findApplicantForScholar(array $old): ?Applicant
    {
        $query = Applicant::query()
            ->where('status', 'approved')
            ->whereNull('archived_at');

        if (! empty($old['user_id'])) {
            $query->where('user_id', $old['user_id']);
        } elseif (! empty($old['birthdate'])) {
            $query->where('first_name', $old['first_name'] ?? null)
                ->where('last_name', $old['last_name'] ?? null)
                ->whereDate('birthdate', Carbon::parse($old['birthdate'])->toDateString());
        } else {
            return null;
        }

        return $query->latest('id')->first();
    }

    // ═════════════════════════════════════════════════════════════════════
    //  Helpers
    // ═════════════════════════════════════════════════════════════════════

    /** Save without firing model events, so the two sides can't loop. */
    protected static function quietUpdate(Model $model, array $attributes): void
    {
        $model->forceFill($attributes);
        $model->saveQuietly();
    }

    /**
     * Moves one slot from the old type to the new type.
     * Throws if the new type has no slot left (rolls back the transaction).
     */
    protected static function moveSlot(?int $oldTypeId, ?int $newTypeId): void
    {
        if (! $newTypeId || (int) $oldTypeId === (int) $newTypeId) {
            return;
        }

        $taken = TypeOfScholarship::whereKey($newTypeId)
            ->where('slots', '>', 0)
            ->decrement('slots');

        if (! $taken) {
            throw new \RuntimeException('There are no remaining slots for the selected type of scholarship.');
        }

        if ($oldTypeId) {
            TypeOfScholarship::whereKey($oldTypeId)->increment('slots');
        }
    }

    /**
     * True when two values really differ (null and '' are the same,
     * 80 / "80" / "80.00" are the same, dates compare by day only).
     */
    public static function valueChanged(string $field, $old, $new): bool
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