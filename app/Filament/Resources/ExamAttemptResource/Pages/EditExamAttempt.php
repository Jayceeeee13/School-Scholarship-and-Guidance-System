<?php

namespace App\Filament\Resources\ExamAttemptResource\Pages;

use App\Filament\Resources\ExamAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamAttempt extends EditRecord
{
    protected static string $resource = ExamAttemptResource::class;

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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterFill(): void
    {
        $this->originalStateHash = $this->hashState($this->data);
    }

    /**
     * If nothing changed: no validation, no save, no "Saved" toast.
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

    protected function hashState(array $state): string
    {
        return md5(json_encode($this->normalizeState($state)));
    }

    /**
     * Makes harmless differences (null vs '', 3 vs "3", true vs 1)
     * not count as changes.
     */
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
}