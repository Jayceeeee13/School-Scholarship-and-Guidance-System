<?php

namespace App\Filament\Resources\SupportNeededResource\Pages;

use App\Filament\Resources\SupportNeededResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupportNeeded extends EditRecord
{
    protected static string $resource = SupportNeededResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
