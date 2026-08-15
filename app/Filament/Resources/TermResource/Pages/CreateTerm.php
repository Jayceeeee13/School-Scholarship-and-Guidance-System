<?php

namespace App\Filament\Resources\TermResource\Pages;

use App\Filament\Resources\TermResource;
use App\Models\Term;
use Filament\Resources\Pages\CreateRecord;

class CreateTerm extends CreateRecord
{
    protected static string $resource = TermResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If setting this term as active, deactivate all others first
        if (! empty($data['is_active'])) {
            Term::query()->update(['is_active' => false]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}