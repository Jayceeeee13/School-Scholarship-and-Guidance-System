<?php

namespace App\Filament\Resources\TermResource\Pages;

use App\Filament\Resources\TermResource;
use App\Models\Term;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTerm extends EditRecord
{
    protected static string $resource = TermResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If setting this term as active, deactivate all others first
        if (! empty($data['is_active'])) {
            Term::where('id', '!=', $this->record->id)->update(['is_active' => false]);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}