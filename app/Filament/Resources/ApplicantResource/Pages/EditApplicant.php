<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use App\Services\ScholarSyncService;
use App\Traits\LogsCustomActivity;
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
     *
     * 1. If nothing changed, skip the save (so no "Saved" notification)
     *    and go back to the list.
     * 2. If the type of scholarship is being changed on an approved applicant
     *    and the new type has no slots left, stop and tell the user.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remember the pre-save values for the scholar sync.
        $this->originalApplicant = $this->getRecord()->getRawOriginal();

        $copy = $this->getRecord()->replicate();
        $copy->setRawAttributes($this->getRecord()->getAttributes(), true);
        $copy->exists = true;
        $copy->fill($data);

        if (! $copy->isDirty()) {
            $this->redirect($this->getRedirectUrl());

            $this->halt();
        }

        if ($error = ScholarSyncService::ensureSlotForApplicantTypeChange($this->getRecord(), $data)) {
            Notification::make()
                ->title('Type of scholarship not changed')
                ->body($error)
                ->danger()
                ->send();

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
                ->body($e->getMessage() . ' (see laravel.log for details)')
                ->warning()
                ->send();
        }
    }

    /**
     * Copies the fields changed on this applicant to the matching
     * Institutional Scholar and Scholars records, and moves a slot when the
     * type of scholarship changed. See ScholarSyncService for the details.
     */
    protected function syncToScholars(): void
    {
        $result = ScholarSyncService::syncFromApplicant(
            $this->getRecord(),
            $this->originalApplicant
        );

        if (! $result) {
            return;
        }

        $institutional = $result['institutional'];

        $this->logCustomActivity(
            $institutional,
            'institutional_scholars',
            'updated',
            "Updated {$institutional->first_name} {$institutional->last_name} from the applicant record ("
                . implode(', ', array_keys($result['changes'])) . ')'
        );

        Notification::make()
            ->title('Scholar record updated too')
            ->body('The matching scholar record was updated with the same changes.'
                . ($result['slots'] ? ' Scholarship slots were adjusted.' : ''))
            ->success()
            ->send();
    }
}