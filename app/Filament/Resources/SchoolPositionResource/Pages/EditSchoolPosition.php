<?php

namespace App\Filament\Resources\SchoolPositionResource\Pages;

use App\Filament\Resources\SchoolPositionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchoolPosition extends EditRecord
{
    protected static string $resource = SchoolPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
