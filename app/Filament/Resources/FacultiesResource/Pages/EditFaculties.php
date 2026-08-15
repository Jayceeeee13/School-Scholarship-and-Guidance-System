<?php

namespace App\Filament\Resources\FacultiesResource\Pages;

use App\Filament\Resources\FacultiesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaculties extends EditRecord
{
    protected static string $resource = FacultiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
