<?php

namespace App\Filament\Resources\ModeOfCounselingResource\Pages;

use App\Filament\Resources\ModeOfCounselingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModeOfCounseling extends EditRecord
{
    protected static string $resource = ModeOfCounselingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
