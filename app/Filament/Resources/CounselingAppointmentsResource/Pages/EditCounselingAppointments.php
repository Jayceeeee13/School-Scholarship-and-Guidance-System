<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\Pages;

use App\Filament\Resources\CounselingAppointmentsResource;
use App\Models\CounselingAppointments;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCounselingAppointments extends EditRecord
{
    protected static string $resource = CounselingAppointmentsResource::class;

    /**
     * Hash of the form state right after it was filled. Must be public so
     * Livewire keeps it between requests (protected props are reset).
     */
    public ?string $originalStateHash = null;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    /**
     * Runs once the form has been filled and hydrated (all tabs, the
     * logforms repeater, anecdotals and the endorsement fieldset).
     */
    protected function afterFill(): void
    {
        $this->originalStateHash = $this->hashState($this->data);
    }

    /**
     * Runs before validation and before mutateFormDataBeforeSave(), so when
     * nothing changed there are no validation errors, no endorsement delete,
     * no "Saved" notification and no update at all.
     */
    protected function beforeValidate(): void
    {
        if ($this->originalStateHash === null) {
            return;
        }

        if ($this->hashState($this->data) !== $this->originalStateHash) {
            return;
        }

        $this->redirect($this->getRedirectUrl());

        $this->halt();
    }

    /**
     * Normalizes the state so harmless differences (null vs '', 80 vs "80",
     * true vs 1) never count as a change, then hashes it.
     */
    protected function hashState(array $state): string
    {
        return md5(json_encode($this->normalizeState($state)));
    }

    protected function normalizeState(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($v) => $this->normalizeState($v), $value);
        }

        if ($value === null || $value === false) {
            return '';
        }

        if ($value === true) {
            return '1';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /*
        |--------------------------------------------------------------------------
        | Endorsement
        |--------------------------------------------------------------------------
        */

        if (empty($data['has_endorsement'])) {

            // Delete existing endorsement when toggle is disabled
            $this->record->endorsement()?->delete();

            unset($data['endorsement']);
        }

        // has_endorsement is not an actual database column
        unset($data['has_endorsement']);

        /*
        |--------------------------------------------------------------------------
        | Completed Appointment Protection
        |--------------------------------------------------------------------------
        |
        | Once the counseling session is completed, don't allow an edit
        | to accidentally change it back to pending/approved.
        |
        */

        if ($this->record->status === 'completed') {
            $data['status'] = 'completed';
        }

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        /*
        |--------------------------------------------------------------------------
        | Endorsement Information
        |--------------------------------------------------------------------------
        */

        $data['endorsement']['name'] = trim(
            "{$record->first_name} " .
            "{$record->middle_name} " .
            "{$record->last_name}"
        );

        $data['endorsement']['course_and_year'] =
            $record->course_and_year;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}