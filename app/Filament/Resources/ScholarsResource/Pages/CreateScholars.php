<?php

namespace App\Filament\Resources\ScholarsResource\Pages;

use App\Filament\Resources\ScholarsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScholars extends CreateRecord
{
    protected static string $resource = ScholarsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
 
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Student ID and batch_no can be left empty and assigned later
        return $data;
    }
}
