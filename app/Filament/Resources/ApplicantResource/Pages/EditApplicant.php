<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplicant extends EditRecord
{
    protected static string $resource = ApplicantResource::class;

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
}