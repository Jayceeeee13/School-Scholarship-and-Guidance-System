<?php

namespace App\Filament\Resources\TypeOfApplicationResource\Pages;

use App\Filament\Resources\TypeOfApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTypeOfApplication extends EditRecord
{
    protected static string $resource = TypeOfApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
