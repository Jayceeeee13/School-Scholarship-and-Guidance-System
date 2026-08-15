<?php

namespace App\Filament\Resources\TypeOfScholarshipResource\Pages;

use App\Filament\Resources\TypeOfScholarshipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTypeOfScholarship extends EditRecord
{
    protected static string $resource = TypeOfScholarshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
