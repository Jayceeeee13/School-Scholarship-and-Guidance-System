<?php

namespace App\Filament\Resources\CounselingLogformsResource\Pages;

use App\Filament\Resources\CounselingLogformsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCounselingLogforms extends EditRecord
{
    protected static string $resource = CounselingLogformsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
