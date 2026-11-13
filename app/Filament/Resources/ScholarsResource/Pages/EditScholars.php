<?php

namespace App\Filament\Resources\ScholarsResource\Pages;

use App\Filament\Resources\ScholarsResource;
use App\Services\ScholarApplicantSync;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditScholars extends EditRecord
{
    use LogsCustomActivity;

    protected static string $resource = ScholarsResource::class;

    /**
     * Scholar values exactly as they were in the database before this save.
     * Used to find the matching applicant (by the OLD name/birthdate) and to
     * work out which fields were actually changed.
     */
    protected array $originalScholar = [];

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
     * If nothing changed, skip the save (no "Saved" notification,
     * no model events) and go back to the list.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remember the pre-save values for the applicant sync.
        $this->originalScholar = $this->getRecord()->getRawOriginal();

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
     * Runs after the scholar has been saved: copies the changed fields
     * (including type of scholarship) to the matching institutional
     * scholar and approved applicant.
     */
    protected function afterSave(): void
    {
        try {
            $result = ScholarApplicantSync::fromScholarRecord($this->getRecord(), $this->originalScholar);

            ScholarApplicantSync::notify($result);

            if ($result && ! empty($result['updated'])) {
                $this->logCustomActivity(
                    $this->getRecord(),
                    'scholars',
                    'updated',
                    ScholarApplicantSync::describe($result)
                );
            }
        } catch (\Throwable $e) {
            // The scholar is already saved; never break the page over the sync.
            Log::error('Scholar -> applicant sync failed', [
                'scholar_id' => $this->getRecord()->id,
                'error'      => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
            ]);

            Notification::make()
                ->title('Scholar saved, but the linked records could not be updated')
                ->body('See laravel.log for details.')
                ->warning()
                ->send();
        }
    }
}