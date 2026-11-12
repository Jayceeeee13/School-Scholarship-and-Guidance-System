<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use App\Filament\Resources\ScholarsResource\Pages\ListScholars;
use App\Models\Gender;
use App\Models\InstitutionalScholar;
use App\Models\Program;
use App\Traits\LogsCustomActivity;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditApplicant extends EditRecord
{
    use LogsCustomActivity;

    protected static string $resource = ApplicantResource::class;

    /**
     * Applicant values exactly as they were in the database before this save.
     * Used to find the matching scholar (by the OLD name/birthdate) and to
     * work out which fields were actually changed.
     */
    protected array $originalApplicant = [];

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Runs after validation, right before the record is saved.
     * If nothing changed, skip the save (so no "Saved" notification)
     * and go back to the list.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remember the pre-save values for syncToScholars().
        $this->originalApplicant = $this->getRecord()->getRawOriginal();

        $copy = $this->getRecord()->replicate();
        $copy->setRawAttributes($this->getRecord()->getAttributes(), true);
        $copy->exists = true;
        $copy->fill($data);

        if (! $copy->isDirty()) {
            $this->redirect($this->getRedirectUrl());

            $this->halt();
        }

        return $data;
    }

    /**
     * Runs after the applicant has been saved.
     */
    protected function afterSave(): void
    {
        try {
            $this->syncToScholars();
        } catch (\Throwable $e) {
            // The applicant is already saved; never break the page over the sync.
            Log::error('Applicant -> scholar sync failed', [
                'applicant_id' => $this->getRecord()->id,
                'error'        => $e->getMessage(),
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
            ]);

            Notification::make()
                ->title('Applicant saved, but the scholar record could not be updated')
                ->body('See laravel.log for details.')
                ->warning()
                ->send();
        }
    }

    /**
     * Copies the fields that were changed on this applicant to the matching
     * Institutional Scholar and Scholars records.
     *
     * - Only applicants that are already approved have a scholar record.
     * - Only fields that actually changed in this edit are copied, so any
     *   later edits made directly on the scholar are not overwritten.
     * - Type of scholarship is NOT copied: changing it also means moving
     *   slots between the two types, which should be done deliberately.
     */
    protected function syncToScholars(): void
    {
        $applicant = $this->getRecord();
        $old       = $this->originalApplicant;

        if ($applicant->status !== 'approved' || empty($old)) {
            return;
        }

        $new = $applicant->getAttributes();

        // ── 1. Work out which fields changed ─────────────────────────────
        $changes = [];

        foreach (['first_name', 'middle_name', 'last_name', 'extension_name', 'year_level', 'benefit'] as $field) {
            if ($this->valueChanged($field, $old[$field] ?? null, $new[$field] ?? null)) {
                $changes[$field] = $new[$field] ?? null;
            }
        }

        if ($this->valueChanged('birthdate', $old['birthdate'] ?? null, $new['birthdate'] ?? null)) {
            $changes['birthdate'] = ! empty($new['birthdate'])
                ? Carbon::parse($new['birthdate'])->toDateString()
                : null;
        }

        // Scholars store the NAME of the gender / program, not the id.
        if ($this->valueChanged('gender_id', $old['gender_id'] ?? null, $new['gender_id'] ?? null)) {
            $sex = Gender::find($new['gender_id'] ?? null)?->name;

            if ($sex) {
                $changes['sex'] = $sex;
            }
        }

        if ($this->valueChanged('program_id', $old['program_id'] ?? null, $new['program_id'] ?? null)) {
            $program = Program::find($new['program_id'] ?? null)?->name;

            if ($program) {
                $changes['program'] = $program;
            }
        }

        if (empty($changes)) {
            return;
        }

        // ── 2. Find the institutional scholar (using the OLD values) ────
        $institutional = null;

        if (! empty($old['user_id'])) {
            $institutional = InstitutionalScholar::where('user_id', $old['user_id'])
                ->latest('id')
                ->first();
        }

        if (! $institutional && ! empty($old['birthdate'])) {
            $institutional = InstitutionalScholar::where('first_name', $old['first_name'] ?? null)
                ->where('last_name', $old['last_name'] ?? null)
                ->whereDate('birthdate', Carbon::parse($old['birthdate'])->toDateString())
                ->latest('id')
                ->first();
        }

        if (! $institutional) {
            return;
        }

        // Find the matching Scholars row BEFORE updating the institutional
        // record, because the match uses the institutional record's values.
        $scholar = ListScholars::findMatchingScholar($institutional);

        // ── 3. Apply the changes ────────────────────────────────────────
        $institutional->update($changes);
        $scholar?->update($changes);

        $this->logCustomActivity(
            $institutional,
            'institutional_scholars',
            'updated',
            "Updated {$institutional->first_name} {$institutional->last_name} from the applicant record ("
                . implode(', ', array_keys($changes)) . ')'
        );

        Notification::make()
            ->title('Scholar record updated too')
            ->body('The matching scholar record was updated with the same changes.')
            ->success()
            ->send();
    }

    /**
     * True when two values really differ (null and '' are the same,
     * 80 / "80" / "80.00" are the same, dates compare by day only).
     */
    protected function valueChanged(string $field, $old, $new): bool
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