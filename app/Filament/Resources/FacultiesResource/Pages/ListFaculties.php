<?php

namespace App\Filament\Resources\FacultiesResource\Pages;

use App\Filament\Resources\FacultiesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaculties extends ListRecords
{
    protected static string $resource = FacultiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
