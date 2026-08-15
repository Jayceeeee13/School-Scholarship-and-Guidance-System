<?php

namespace App\Filament\Resources\StaffsResource\Pages;

use App\Filament\Resources\StaffsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffs extends EditRecord
{
    protected static string $resource = StaffsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
