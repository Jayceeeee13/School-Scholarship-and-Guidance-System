<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use App\Services\ScholarApplicantSync;
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
     * If nothing changed, skip the save (so no "Saved" notification)
     * and go back to the list.
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

        return $data;
    }

    /**
     * Runs after the applicant has been saved: copies the changed fields
     * (including type of scholarship) to the matching scholar records.
     */
    protected function afterSave(): void
    {
        try {
            $result = ScholarApplicantSync::fromApplicant($this->getRecord(), $this->originalApplicant);

            ScholarApplicantSync::notify($result);

            if ($result && ! empty($result['updated'])) {
                $this->logCustomActivity(
                    $this->getRecord(),
                    'applicants',
                    'updated',
                    ScholarApplicantSync::describe($result)
                );
            }
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
}