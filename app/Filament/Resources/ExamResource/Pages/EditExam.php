<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExam extends EditRecord
{
    protected static string $resource = ExamResource::class;

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
     * If nothing changed, skip the save (no "Saved" notification)
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