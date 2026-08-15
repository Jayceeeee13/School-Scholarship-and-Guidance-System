<?php

namespace App\Filament\Resources\CounselingTimeSlotResource\Pages;

use App\Filament\Resources\CounselingTimeSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCounselingTimeSlot extends EditRecord
{
    protected static string $resource = CounselingTimeSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
