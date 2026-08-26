<?php

namespace App\Filament\Resources\TypeOfScholarshipResource\Pages;

use App\Filament\Resources\TypeOfScholarshipResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTypeOfScholarship extends CreateRecord
{
    protected static string $resource = TypeOfScholarshipResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
