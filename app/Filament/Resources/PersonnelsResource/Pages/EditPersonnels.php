<?php

namespace App\Filament\Resources\PersonnelsResource\Pages;

use App\Filament\Resources\PersonnelsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonnels extends EditRecord
{
    protected static string $resource = PersonnelsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
