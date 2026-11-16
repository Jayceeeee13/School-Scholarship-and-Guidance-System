<?php

namespace App\Filament\Resources\ScholarsResource\Pages;

use App\Filament\Resources\ScholarsResource;
use App\Services\ScholarSyncService;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditScholars extends EditRecord
{
    use LogsCustomActivity;

    protected static string $resource = ScholarsResource::class;

    /** Scholar values exactly as they were before this save. */
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
     * 1. If nothing changed, skip the save (no "Saved" notification,
     *    no model events) and go back to the list.
     * 2. If the type of scholarship is being changed to one with no slots
     *    left (and the scholar is linked to an applicant), stop.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->originalScholar = $this->getRecord()->getRawOriginal();

        $copy = $this->getRecord()->replicate();
        $copy->setRawAttributes($this->getRecord()->getAttributes(), true);
        $copy->exists = true;
        $copy->fill($data);

        if (! $copy->isDirty()) {
            $this->redirect($this->getRedirectUrl());

            $this->halt();
        }

        if ($error = ScholarSyncService::ensureSlotForScholarTypeChange($this->originalScholar, $data)) {
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
     * After the scholar is saved, copy the changes to the Institutional
     * Scholar record and to the Applicant (and move a slot if the type
     * of scholarship changed).
     */
    protected function afterSave(): void
    {
        try {
            $result = ScholarSyncService::syncFromScholarRecord(
                $this->getRecord(),
                $this->originalScholar
            );

            if (! $result || ! $result['applicant']) {
                return;
            }

            $applicant = $result['applicant'];

            $this->logCustomActivity(
                $applicant,
                'applicants',
                'updated',
                "Updated {$applicant->first_name} {$applicant->last_name} from the scholar record ("
                    . implode(', ', array_keys($result['changes'])) . ')'
            );

            Notification::make()
                ->title('Applicant record updated too')
                ->body('The matching applicant was updated with the same changes.'
                    . ($result['slots'] ? ' Scholarship slots were adjusted.' : ''))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            // The scholar is already saved; never break the page over the sync.
            Log::error('Scholar -> applicant sync failed', [
                'scholar_id' => $this->getRecord()->id,
                'error'      => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
            ]);

            Notification::make()
                ->title('Scholar saved, but the applicant record could not be updated')
                ->body($e->getMessage() . ' (see laravel.log for details)')
                ->warning()
                ->send();
        }
    }
}